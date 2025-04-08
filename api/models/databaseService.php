<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;

class DatabaseService {
    private $db;

    function __construct($db) {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        $port = $_ENV['DB_PORT'];

        $this->db = new mysqli($host, $user, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function Query($query) {
        $result = $this->db->query($query);
        if ($result === false) {
            return null;
        }
        return $result;
    }

    public function GetConnection() {
        return $this->db;
    }
}