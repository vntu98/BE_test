<?php

class Database {
    private $servername;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct()
    {
        $this->servername = "fresh.test";
        $this->username = "root";
        $this->password = "";
        $this->database = "db_test";
        $this->conn = null;
    }

    public function connect() {

        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $this->conn;
    }
}
