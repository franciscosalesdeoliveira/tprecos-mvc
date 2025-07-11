<?php

namespace Source\App\Models;

use PDO;
use Source\helpers\Connection;

class Contato
{
    /**
     * Salva um contato no banco de dados
     */
    public static function salvar(array $data): bool
    {
        try {
            $pdo = Connection::getPDO();

            $stmt = $pdo->prepare("
                INSERT INTO contatos (nome, email, mensagem) 
                VALUES (:nome, :email, :mensagem)
            ");

            return $stmt->execute([
                ":nome"     => $data["nome"],
                ":email"    => $data["email"],
                ":mensagem" => $data["mensagem"]
            ]);
        } catch (\PDOException $e) {
            // Logar erro se quiser
            return false;
        }
    }
}
