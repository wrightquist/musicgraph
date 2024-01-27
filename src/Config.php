<?php
if (gethostname() === "cs4640"){
    class Config
    {
        public static $db = [
            "host" => "localhost",
            "port" => 5432,
            "user" => "wru3rm",
            "pass" => "m0opwnADPiY-",
            "database" => "wru3rm"
        ];
    }
} else {
    class Config
    {
        public static $db = [
            "host" => "db",
            "port" => 5432,
            "user" => "localuser",
            "pass" => "cs4640LocalUser!",
            "database" => "example"
        ];
    }
}