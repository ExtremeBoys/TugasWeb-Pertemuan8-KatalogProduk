<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'inventaris_db';
        $username = 'root';
        $password = '';

        $this->conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
