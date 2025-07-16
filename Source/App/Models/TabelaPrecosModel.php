<?php
// models/TabelaPrecosModel.php

namespace App\Models;

use PDO;


class TabelaPrecosModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function verificarColunas($tabela)
    {
        try {
            $checkStmt = $this->pdo->prepare("SELECT * FROM $tabela LIMIT 1");
            $checkStmt->execute();
            $colunas = [];
            for ($i = 0; $i < $checkStmt->columnCount(); $i++) {
                $colMeta = $checkStmt->getColumnMeta($i);
                $colunas[] = strtolower($colMeta['name']);
            }
            return $colunas;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getProdutos($grupoSelecionado, $temColunaAtivoGrupos, $temColunaAtivoProdutos, $temColunaUpdatedAt)
    {
        $dbType = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        $sql = "SELECT p.nome as produto, p.preco, g.nome as grupo, 
                p.id as produto_id, g.id as grupo_id" .
            ($temColunaUpdatedAt ? ", p.updated_at as ultima_atualizacao" : ", NULL as ultima_atualizacao") . "
               FROM produtos p
               JOIN grupos g ON p.grupo_id = g.id
               WHERE 1=1";

        if ($temColunaAtivoGrupos) {
            $sql .= $dbType == 'pgsql' ? " AND g.ativo = TRUE" : " AND g.ativo = 1";
        }
        if ($temColunaAtivoProdutos) {
            $sql .= $dbType == 'pgsql' ? " AND p.ativo = TRUE" : " AND p.ativo = 1";
        }

        if ($grupoSelecionado !== 'todos') {
            $sql .= " AND g.id = :grupo_id";
        }

        $sql .= " ORDER BY g.nome, p.nome";

        $stmt = $this->pdo->prepare($sql);

        if ($grupoSelecionado !== 'todos') {
            $stmt->bindParam(':grupo_id', $grupoSelecionado, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGrupos($temColunaAtivoGrupos)
    {
        $dbType = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        $sql = "SELECT id, nome FROM grupos WHERE 1=1";

        if ($temColunaAtivoGrupos) {
            $sql .= $dbType == 'pgsql' ? " AND ativo = TRUE" : " AND ativo = 1";
        }

        $sql .= " ORDER BY nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
