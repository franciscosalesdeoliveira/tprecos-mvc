<?php

namespace Source\App\Models;

use CoffeeCode\DataLayer\DataLayer;
use PDO;
use PDOException;

class Propagandas extends DataLayer
{
    private $pdo;
    private $uploadDir;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->uploadDir = 'uploads/propagandas/';
        parent::__construct("propagandas", ["titulo", "descricao", "imagem", "tipo_imagem", "ativo", "ordem"]);

        // Criar diretório de uploads se não existir
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function getPropagandasAtivas()
    {
        $dbType = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $sql = "SELECT id, titulo, descricao, imagem, tipo_imagem, ordem FROM propagandas WHERE " .
            ($dbType == 'pgsql' ? "ativo = TRUE" : "ativo = 1") . " ORDER BY ordem, id";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPropagandas()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM propagandas ORDER BY ordem, titulo");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Erro ao listar propagandas: " . $e->getMessage());
        }
    }

    public function getPropagandaById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM propagandas WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Erro ao buscar propaganda: " . $e->getMessage());
        }
    }

    public function createPropaganda($dados)
    {
        try {
            if ($dados['tipo_imagem'] == 'url') {
                $stmt = $this->pdo->prepare("INSERT INTO propagandas (titulo, descricao, imagem, tipo_imagem, ativo, ordem) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $dados['titulo'],
                    $dados['descricao'],
                    $dados['imagem'],
                    'url',
                    $dados['ativo'],
                    $dados['ordem']
                ]);
            } else {
                $stmt = $this->pdo->prepare("INSERT INTO propagandas (titulo, descricao, imagem, tipo_imagem, ativo, ordem) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $dados['titulo'],
                    $dados['descricao'],
                    $dados['imagem'],
                    'local',
                    $dados['ativo'],
                    $dados['ordem']
                ]);
            }
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new \Exception("Erro ao criar propaganda: " . $e->getMessage());
        }
    }


    public function updatePropaganda($id, $dados, $atualizarImagem = false)
    {
        try {
            // CORREÇÃO: Normalizar o campo "ativo" considerando diferentes formatos
            // $ativo = 'N'; // Padrão

            if (isset($dados['ativo'])) {
                $valorAtivo = $dados['ativo'];
                // Aceitar 'S', '1', 1, true, 'true' como ativo
                if ($valorAtivo === 'S' || $valorAtivo === '1' || $valorAtivo === 1 || $valorAtivo === true || $valorAtivo === 'true') {
                    $ativo = 'S';
                }
            }

            if ($atualizarImagem) {
                $stmt = $this->pdo->prepare("UPDATE propagandas SET 
            titulo = ?, descricao = ?, imagem = ?, tipo_imagem = ?, ativo = ?, ordem = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?");
                $stmt->execute([
                    $dados['titulo'],
                    $dados['descricao'],
                    $dados['imagem'],
                    $dados['tipo_imagem'],
                    $ativo,
                    $dados['ordem'],
                    $id
                ]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE propagandas SET 
            titulo = ?, descricao = ?, ativo = ?, ordem = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?");
                $stmt->execute([
                    $dados['titulo'],
                    $dados['descricao'],
                    $ativo,
                    $dados['ordem'],
                    $id
                ]);
            }

            return true;
        } catch (PDOException $e) {
            throw new \Exception("Erro ao atualizar propaganda: " . $e->getMessage());
        }
    }





    public function processarUpload($arquivo)
    {
        if (!isset($arquivo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Erro no upload do arquivo.");
        }

        // Verificar extensão
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];

        if (!in_array($extensao, $extensoesPermitidas)) {
            throw new \Exception("Formato de arquivo não permitido. Use: " . implode(', ', $extensoesPermitidas));
        }

        // Gerar nome único para o arquivo
        $nomeArquivo = 'propaganda_' . time() . '_' . uniqid() . '.' . $extensao;

        // Mover o arquivo para a pasta de uploads
        if (!move_uploaded_file($arquivo['tmp_name'], $this->uploadDir . $nomeArquivo)) {
            throw new \Exception("Falha ao salvar o arquivo. Verifique as permissões da pasta.");
        }

        return $nomeArquivo;
    }

    public function validarUrl($url)
    {
        if (empty($url)) {
            throw new \Exception("Por favor, forneça uma URL válida para a imagem.");
        }

        // Detectar e converter URL do Google Drive se necessário
        if (strpos($url, 'drive.google.com/file/d/') !== false) {
            preg_match('/\/d\/([^\/]*)/', $url, $matches);
            if (isset($matches[1])) {
                $url = "https://drive.google.com/file/d/" . $matches[1] . "/view";
            }
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("A URL fornecida não é válida.");
        }

        return $url;
    }

    public function getImagemUrl($propaganda)
    {
        $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';

        if ($tipoImagem == 'url') {
            $url = $propaganda['imagem'];
            // Converter URL do Google Drive para visualização direta
            if (strpos($url, 'drive.google.com/file/d/') !== false) {
                preg_match('/\/d\/([^\/]*)/', $url, $matches);
                if (isset($matches[1])) {
                    return "https://drive.google.com/uc?export=view&id=" . $matches[1];
                }
            }
            return $url;
        } else {
            return $this->uploadDir . $propaganda['imagem'];
        }
    }
    public function verificaImagemExiste($propaganda)
    {
        $tipoImagem = $propaganda['tipo_imagem'] ?? 'local';

        if ($tipoImagem == 'url') {
            return !empty($propaganda['imagem']);
        } else {
            return !empty($propaganda['imagem']) && file_exists($this->uploadDir . $propaganda['imagem']);
        }
    }
}
