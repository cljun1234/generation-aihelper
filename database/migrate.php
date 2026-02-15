<?php
require_once __DIR__ . '/../config/database.php';

try {
    // Create the database file if it doesn't exist for SQLite
    if (getenv('DB_DRIVER') === 'sqlite') {
        $dbFile = __DIR__ . '/../database/trustabee.sqlite';
        if (!file_exists($dbFile)) {
            touch($dbFile);
            echo "Created SQLite database file: $dbFile\n";
        }
    }

    $pdo = Database::getInstance();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    echo "Using database driver: $driver\n";

    // 1. Ensure Base Schema (users table) exists
    // Wait, the original schema.sql uses MySQL specific syntax like INT AUTO_INCREMENT.
    // I need a SQLite compatible base schema too if I want to test locally.

    if ($driver === 'sqlite') {
        // Simple users table for SQLite testing
        $baseSql = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";
        $pdo->exec($baseSql);

        // Use the SQLite specific tools schema
        $toolsSqlFile = __DIR__ . '/001_create_tools_tables_sqlite.sql';
    } else {
        // Run original schema if needed (assuming MySQL)
        // Check if users table exists first
        try {
            $pdo->query("SELECT 1 FROM users LIMIT 1");
        } catch (PDOException $e) {
            // Table doesn't exist, run base schema
            $baseSql = file_get_contents(__DIR__ . '/schema.sql');
            if ($baseSql) $pdo->exec($baseSql);
        }

        $toolsSqlFile = __DIR__ . '/001_create_tools_tables.sql';
    }

    // 2. Run Tools Migration
    if (file_exists($toolsSqlFile)) {
        $sql = file_get_contents($toolsSqlFile);
        if ($sql) {
            // Split by semicolon for SQLite which sometimes prefers one statement per exec() call
            // specific to some drivers, but PDO usually handles multiple statements.
            // Let's try executing the whole block.
            try {
                $pdo->exec($sql);
                echo "Tools migration executed successfully using $toolsSqlFile.\n";
            } catch (PDOException $e) {
                // Fallback to splitting by semicolon
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($statements as $stmt) {
                    if (!empty($stmt)) {
                        $pdo->exec($stmt);
                    }
                }
                echo "Tools migration executed statement by statement.\n";
            }
        } else {
            echo "Error: Tools SQL file is empty.\n";
        }
    } else {
        echo "Error: Migration file not found: $toolsSqlFile\n";
    }

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
