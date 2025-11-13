<?php

namespace Src\Config;

use PDO;
use PDOException;

class Database
{
    private $host = '127.0.0.1';
    private $db = 'apiphp'; // nama database kamu
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';
    private $conn;

    public function __construct($config = [])
    {
        // tidak perlu apa-apa di sini
    }

    public function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
            return $this->conn;
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
}
