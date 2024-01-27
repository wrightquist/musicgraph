<?php
if (gethostname() === "cs4640") {
    include "/students/wru3rm/students/wru3rm/src/Database.php";
} else {
    include "src/Database.php";
}
$login = new LoginController();
$login->run();

//todo: trim usernames/passwords before checking if empty

class LoginController
{
    private $db;

    public function __construct()
    {
        session_start();
        $this->db = new Database();
    }

    /**
     * url: login.php?action=login|signup|logout
     * all requests must be posts(for pw network security)
     * login and signup return json, logout redirects to homepage
     */
    public function run()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_GET["action"])) {
                $errors["all"] = "Form submission error. please try again.";
            } else if ($_GET["action"] === "login") {
                $errors = $this->login();
            } else if ($_GET["action"] === "signup") {
                $errors = $this->signup();
            } else if ($_GET["action"] === "logout") {
                $this->logout();
            } else {
                $errors["all"] = "Form submission error. please try again.";
            }

            $data = [];
            if (!empty($errors)) {
                $data['success'] = false;
                $data['errors'] = $errors;
            } else {
                $data['success'] = true;
                $data['message'] = 'Success!';
            }
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    function login()
    {
        $errors = [];
        //ensure username+pw provided
        if (empty($_POST["password"])) $errors["password"] = "Password Required.";
        if (empty($_POST["username"])) {
            $errors["username"] = "Username Required.";
            return $errors;
        }
        //ensure user with username exists
        $res = $this->db->query("select * from users where username = $1;", $_POST["username"]);
        if (empty($res)) {
            $errors["username"] = "Incorrect Username";
            return $errors;
        }
        if (!empty($errors)) return $errors;
        //ensure correct pw
        if (!password_verify($_POST["password"], $res[0]["password"])) return ["password" => "Incorrect Password."];
        //log in
        $_SESSION["id"] = $res[0]["id"];
        $_SESSION["username"] = $res[0]["username"];
        return $errors;
    }

    function signup()
    {
        $errors = [];
        //ensure username+pw+pw confirmation provided
        if (empty($_POST["password"])) $errors["password"] = "Password Required";
        if (empty($_POST["password2"])) $errors["password2"] = "Password Confirmation Required";
        if ($_POST["password"] !== $_POST["password2"] && empty($errors)) $errors["password2"] = "Passwords do not match";
        if (empty($_POST["username"])) {
            $errors["username"] = "Username Required.";
            return $errors;
        }
        //ensure username not taken
        $res = $this->db->query("select * from users where username = $1;", $_POST["username"]);
        if (!empty($res)) {
            $errors["username"] = "Username is already taken";
            return $errors;
        }
        if (!empty($errors)) return $errors;
        //sign up
        $res = $this->db->query("insert into users (username, password) values ($1, $2) returning id;", $_POST["username"], password_hash($_POST["password"], PASSWORD_DEFAULT));
        $_SESSION["id"] = $res[0]["id"];
        $_SESSION["username"] = $_POST["username"];
        return $errors;
    }

    function logout()
    {
        session_destroy();
        session_start();
        header("Location: index.php");
        exit;
    }
}