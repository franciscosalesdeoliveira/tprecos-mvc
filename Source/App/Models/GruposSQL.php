<?php

namespace Source\App\Models;

use PDO;
use Source\helpers\Connection;



class GruposSQL
{
    protected $pdo;

    public function __construct()
    {
        $this->pdo = connection::getPDO(); // sua conexão PDO customizada
    }

    public static function categoriesTreeOrder(): array
    {
        $instance = new static();

        $sql = "
            SELECT *
            FROM grupos
            ORDER BY nome
        ";

        $stmt = $instance->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ) ?: [];
    }
}
