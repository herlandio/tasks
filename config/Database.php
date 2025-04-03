<?php

declare(strict_types=1);

namespace Config;

use \PDO;
use \PDOException;

/* The Database class provides a static method to establish a PDO connection to a MySQL database. */
class Database {
    
    /* The line `private static ?PDO  = null;` is declaring a private static property named
    `` of type `PDO` that can be nullable (`?PDO`). It initializes the property to
    `null`. This property is used to store the PDO connection object for the database. By setting it
    to `null` initially, it indicates that the connection is not yet established. */
    private static ?PDO $connection = null;

    /**
     * The function getConnection() establishes a PDO database connection to a MySQL database named
     * tasks_db.
     * 
     * @return PDO The `getConnection` function is returning a PDO (PHP Data Object) instance
     * representing a connection to a MySQL database.
     */
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
