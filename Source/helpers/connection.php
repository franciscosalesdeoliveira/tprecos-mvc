<?php

namespace Source\helpers;

use PDO;
use PDOException;

class Connection
{
    public static function getPDO(): PDO
    {
        return new PDO("pgsql:host=localhost;dbname=tprecos", "postgres", "admin", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
    
}
