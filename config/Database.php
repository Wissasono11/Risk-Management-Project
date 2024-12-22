<?php

namespace Config;

use mysqli;

class Database
{
    private static $instance = null;
    private $connection;

    // Constructor (private -> enkapsulasi)
    private function __construct()
    {
        $host = 'localhost';
        $user = 'root';
        $pass = '';
        $dbname = 'bbp_risk_management';

        $this->connection = new mysqli($host, $user, $pass, $dbname);
        if ($this->connection->connect_error) {
            throw new \Exception("Connection failed: " . $this->connection->connect_error);
        }
        $this->connection->set_charset("utf8mb4");
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
