<?php

declare(strict_types=1);

namespace Config;

use \PDO;
use \PDOException;

class Database {
    
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO("mysql:host=db;dbname=tasks_db", "root", "root");
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
