<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;

class DatabaseService {
    private $db;

    function __construct() {
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

    public function QueryParams ($sqlQuery, $objectName, $bindtypes, ...$parameters): array | object {
        $stmt = $this->db->prepare($sqlQuery);

        if(is_null($bindtypes)) {
            $bindtypes = $this->defineBindTypes($parameters);
        }

        $stmt->bind_param($bindtypes, ...$parameters);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($obj = $result->fetch_object($objectName)) {
            array_push($items, $obj);
        }
        
        return count($items) > 1 ? $items : $items[0];
    }

    public function GetConnection() {
        return $this->db;
    }

    private function defineBindTypes($parameters): string {
        $types = '';
        foreach ($parameters as $param) {
            if (is_int($param)) $types .= 'i';
            elseif (is_float($param)) $types .= 'd';
            elseif (is_string($param)) $types .= 's';
            else $types .= 'b';
        }
        return $types;
    }
}