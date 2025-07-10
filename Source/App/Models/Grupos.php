<?php

namespace Source\App\Models;

use CoffeeCode\DataLayer\DataLayer;

class Grupos extends DataLayer
{
    public function __construct()
    {
        parent::__construct("grupos", ["nome", "ativo"], "id", false);
    }
    public static function listaParaSelect(): array
    {
        $pdo = \Source\helpers\Connection::getPDO();
        $lista = ['todos' => 'Todos os Grupos'];

        $stmt = $pdo->query("SELECT id, nome FROM grupos ORDER BY nome");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lista[$row['id']] = $row['nome'];
        }

        return $lista;
    }
}
