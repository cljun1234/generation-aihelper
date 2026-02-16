<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Use environment variables if set, otherwise fallback to the user's specific credentials
        // or SQLite for local development if configured.

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db   = getenv('DB_NAME') ?: '';
        $user = getenv('DB_USER') ?: '';
        $pass = getenv('DB_PASS') ?: '';
        $driver = getenv('DB_DRIVER') ?: 'mysql';

        try {
            if ($driver === 'sqlite') {
                // For sandbox testing without MySQL
                $dbPath = __DIR__ . "/../database/trustabee.sqlite";
                $this->pdo = new PDO("sqlite:" . $dbPath);
            } else {
                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                $this->pdo = new PDO($dsn, $user, $pass);
            }

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // In production, log this, don't echo detailed errors to the user
            // But for debugging connection issues, it might be helpful initially.
            die("Database connection failed. Please check your configuration.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
