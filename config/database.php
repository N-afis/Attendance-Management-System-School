<?php

require_once __DIR__ . '/load_env.php';
loadEnv(__DIR__ . '/../.env');

class Database {

    private $db_server;
    private $db_user;
    private $db_pass;
    private $db_name;
    private $conn;

    public function __construct() {
        // Load from .env or fallback to defaults
        $this->db_server = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_user   = $_ENV['DB_USER'] ?? 'root';
        $this->db_pass   = $_ENV['DB_PASS'] ?? '';
        $this->db_name   = $_ENV['DB_NAME'] ?? 'attendancedb';
    }

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->db_server};dbname={$this->db_name};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                $this->conn = new PDO($dsn, $this->db_user, $this->db_pass, $options);

            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return $this->conn;
    }
}
