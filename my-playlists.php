<?php
if (gethostname() === "cs4640") {
    include "/students/wru3rm/students/wru3rm/src/Database.php";
} else {
    include "src/Database.php";
}

$controller = new MyPlaylistsController();
$controller->run();

class MyPlaylistsController
{

    private $db;

    public function __construct()
    {
        session_start();
        $this->db = new Database();
    }

    /**
     * url: my-playlists.php
     * post requests create a new playlist and redirect to it's page
     * get requests display the user's playlists
     * login and signup return json, logout redirects to homepage
     */
    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //ensure logged in
            if (!isset($_SESSION['id'])) {
                header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                return;
            }
            //create playlist and redirect
            $res = $this->db->query("insert into playlists (user_id) values ($1) returning id;", $_SESSION["id"]);
            header("Location: playlist.php?id={$res[0]['id']}");
        } else {
            //redirect if not logged in
            if (!isset($_SESSION['id'])) {
                header("Location: index.php");
                return;
            }
            //get playlists and display them
            $my_playlists = $this->db->query("select * from playlists where user_id = $1", $_SESSION["id"]);
            $liked_playlists = $this->db->query("select playlists.*, users.username from playlists join users on user_id = users.id where public = true and playlists.id in 
                                                (select playlist_id from likes where user_id= $1)", $_SESSION["id"]);
            if (gethostname() === "cs4640") {
                include "/students/wru3rm/students/wru3rm/src/templates/my-playlists.php";
            } else {
                include "src/templates/my-playlists.php";
            }
            myPlaylists($my_playlists, $liked_playlists);
        }
    }

}