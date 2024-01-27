<?php
if (gethostname() === "cs4640") {
    include "/students/wru3rm/students/wru3rm/src/Database.php";
} else {
    include "src/Database.php";
}

$login = new PlaylistController();
$login->run();

class PlaylistController
{
    private $db;
    private $playlist;

    public function __construct()
    {
        session_start();
        $this->db = new Database();
    }

    /**
     * get:
     * url: my-playlists.php?id=[int]
     */
    public function run()
    {
        //if the user is not logged in and there is no id, use a temp playlist
        if (!isset($_GET["id"])) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            return;
        }
        //ensure playlist exists
        $this->playlist = $this->db->query("select * from playlists where id = $1", $_GET["id"])[0];
        if (!$this->playlist) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($this->user_is_owner() || $this->playlist_is_public()) {
                //show json
                $headers = array_change_key_case(getallheaders(), CASE_LOWER);
                if (in_array("application/json", preg_split('/\s*,\s*/', $headers["accept"]))) {
                    header('Content-Type: application/json');
                    echo $this->get_json();
                }
                else
                    //show playlist page
                    $this->show_playlist();
            } else
                header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //user must be logged in to do all post actions
            header('Content-Type: application/json');
            if (!isset($_SESSION["id"])) {
                header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                return;
            }
            if (!isset($_GET["action"])){
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                return;
            }
            switch ($_GET["action"]) {
                case "like":
                    if ($this->user_is_owner() || !$this->playlist_is_public()) {
                        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                        return;
                    }
                    $this->like();
                    break;
                case "unlike":
                    if ($this->user_is_owner() || !$this->playlist_is_public()) {
                        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                        return;
                    }
                    $this->unlike();
                    break;
                case "fork":
                    if (!$this->user_is_owner() && !$this->playlist_is_public()) {
                        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                        return;
                    }
                    $id = $this->fork();
                    echo json_encode(["success" => 1, "redirect" => "playlist.php?id={$id}"]);
                    break;
                case "create_node":
                    if ($this->user_is_owner()) {
                        $id = $this->createNode();
                        echo json_encode(['id' => $id, "success" => 1]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "move_node":
                    if ($this->user_is_owner()) {
                        $res = $this->moveNode();
                        echo json_encode(["success" => $res]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "delete_node":
                    if ($this->user_is_owner()) {
                        $res = $this->deleteNode();
                        echo json_encode(["success" => $res]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "link_nodes":
                    if ($this->user_is_owner()) {
                        $res = $this->linkNodes();
                        echo json_encode(["success" => $res]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "unlink_nodes":
                    if ($this->user_is_owner()) {
                        $res = $this->unlinkNodes();
                        echo json_encode(["success" => $res]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "change_visibility":
                    if ($this->user_is_owner()) {
                        $res = $this->changeVisibility();
                        echo json_encode(["success" => $res]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "change_title":
                    if ($this->user_is_owner()) {
                        $res = $this->changeTitle();
                        echo json_encode(["success" => $res]);
                    }else header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    break;
                case "delete":
                    if (!$this->user_is_owner()) {
                        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                        return;
                    } else {
                        $this->delete();
                    }
            }
        }
    }

    function user_is_owner()
    {
        if (!isset($_SESSION["id"])) return false;
        return ($this->playlist["user_id"] === $_SESSION["id"]);
    }

    function playlist_is_public()
    {
        return ($this->playlist["public"] === "t");
    }

    function like()
    {
        $res = $this->db->query("insert into likes (playlist_id, user_id) values ($1, $2) on conflict do nothing returning user_id",
            $_GET["id"], $_SESSION["id"]);
        if (!empty($res)){
            $this->db->query("update playlists set likes = likes + 1 where id = $1", $_GET["id"]);
        }
    }

    function unlike()
    {
        $res = $this->db->query("delete from likes where playlist_id = $1 and user_id = $2 returning user_id",
            $_GET["id"], $_SESSION["id"]);
        if (!empty($res)){
            $this->db->query("update playlists set likes = likes - 1 where id = $1", $_GET["id"]);
        }
    }

    function fork()
    {
        $id = $this->db->query("insert into playlists (user_id, public) values ($1, true) returning id;",
            $_SESSION["id"])[0]["id"];
        $this->db->query("insert into nodes (id, x, y, yt_id, playlist_id) 
            select id, x, y, yt_id, $1 as playlist_id from nodes where playlist_id = $2;", $id, $_GET["id"]);
        $this->db->query("insert into connections (node_1, node_2, playlist_id) 
            select node_1, node_2, $1 as playlist_id from connections where playlist_id = $2;", $id, $_GET["id"]);
        $this->db->query("update playlists set forks = forks + 1 where id = $1", $_GET["id"]);
        return $id;
    }

    function delete()
    {
        $this->db->query("delete from playlists where id = $1", $_GET["id"]);
    }

    function get_json()
    {
        return json_encode($this->get_playlist_nodes());
    }

    function show_playlist()
    {
        $logged_in = isset($_SESSION["id"]);
        $owner = $logged_in && $this->user_is_owner();
        $temp = $logged_in && isset($_GET["id"]);
        $liked = !$owner && !empty($this->db->query("select * from likes where playlist_id = $1 and user_id = $2",
                $_GET["id"], $_SESSION["id"]));
        $this->playlist["username"] = $this->db->query("select username from users where id = $1", $this->playlist["user_id"])[0]["username"];
        $playlist = $this->playlist;
        if (gethostname() === "cs4640") {
            include "/students/wru3rm/students/wru3rm/src/templates/playlist.php";
        } else {
            include "src/templates/playlist.php";
        }
        playlist($logged_in, $owner, $playlist, $temp, $liked);
    }

    public function get_playlist_nodes()
    {
        $nodes = $this->db->query("select x, y, yt_id, id from nodes where playlist_id = $1", $_GET["id"]);
        $connections = $this->db->query("select node_1, node_2 from connections where playlist_id = $1",
            $_GET["id"]);
        return["nodes"=>$nodes, "connections"=>$connections];
    }

    public function createNode(){
        $res= $this->db->query("insert into nodes (x, y, yt_id, playlist_id) values ($1, $2, $3, $4) returning id",
            $_POST["x"], $_POST["y"], $_POST["yt_id"], $_GET["id"]);
        return $res[0]["id"];
    }

    public function moveNode(){
        return $this->db->query("update nodes set x = $1, y = $2 where id = $3 and playlist_id = $4",
            $_POST["x"], $_POST["y"], $_POST["node_id"], $_GET["id"]);
    }

    public function deleteNode(){
        return $this->db->query("delete from nodes where playlist_id = $1 and id = $2",
            $_GET["id"], $_POST["node_id"]);
    }

    public function linkNodes(){
        return $this->db->query("insert into connections (node_1, node_2, playlist_id) values ($1, $2, $3)",
            min($_POST["node_1"], $_POST["node_2"]), max($_POST["node_1"], $_POST["node_2"]), $_GET["id"]);
    }

    public function unlinkNodes(){
        return $this->db->query("delete from connections where node_1 = $1 and node_2 = $2 and playlist_id = $3",
            min($_POST["node_1"], $_POST["node_2"]), max($_POST["node_1"], $_POST["node_2"]), $_GET["id"]);
    }

    public function changeVisibility(){
        echo $_POST["public"];
        return $this->db->query("update playlists set public = $1 where id = $2",
            $_POST["public"]==="true" ? 1 : 0, $_GET["id"]);
    }

    public function changeTitle(){
        return $this->db->query("update playlists set name = $1 where id = $2",
            $_POST["title"], $_GET["id"]);
    }
}