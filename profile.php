<?php
if (gethostname() === "cs4640") {
    include "/students/wru3rm/students/wru3rm/src/Database.php";
} else {
    include "src/Database.php";
}

$login = new ProfileController();
$login->run();

class ProfileController
{
    private $db;

    public function __construct()
    {
        session_start();
        $this->db = new Database();
    }

    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //update username if logged in
            if (isset($_GET["username"])) {
                $this->db->update("users", ["username" => $_GET["username"]], ["id" => $_SESSION["id"]]);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            //delete account
            $this->db->query("delete from users where id = $1", $_SESSION["id"]);
        } else {
            if (!isset($_SESSION["id"])) {
                header("Location: index.php");
                return;
            }
            //TODO: display profile page
        }
    }
}
