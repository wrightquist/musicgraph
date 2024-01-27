<?php
include ("Config.php");
class Database {
    private $dbConnector;
    public function __construct() {
        $host = Config::$db["host"];
        $user = Config::$db["user"];
        $database = Config::$db["database"];
        $password = Config::$db["pass"];
        $port = Config::$db["port"];

        $this->dbConnector = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");
    }

    public function query($query, ...$params) {
        $res = pg_query_params($this->dbConnector, $query, $params);

        if ($res === false) {
            return false;
        }

        return pg_fetch_all($res);
    }

    public function update($tableName, $data, $condition) {
        $res = pg_update($this->dbConnector, $tableName, $data, $condition, PGSQL_DML_ESCAPE);

        if ($res === false) {
            return false;
        }

        return true;
    }

    public function insert($tableName, $data) {
        $res = pg_insert($this->dbConnector, $tableName, $data, PGSQL_DML_ESCAPE);

        if ($res === false) {
            return false;
        }

        return true;
    }
}
