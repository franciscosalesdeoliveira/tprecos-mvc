<?php

namespace Source\App\Controllers;

use Exception;
use PDO;
use Source\App\Models\Produtos;

class ProductController extends BaseControllerAdmin
{


    public function __construct()
    {
        parent::__construct();
        // Inicializar conexão PDO no construtor
        $this->pdo = new PDO("pgsql:host=localhost;dbname=tprecos", "postgres", "admin");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function index()
    {

        $titulo = "Tabela de Preços";
        $erros = [];
        $success = null;
        $nome = $_POST['nome'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $grupo_id = $_POST['grupo_id'] ?? '';
        $preco = $_POST['preco'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validações simples
            if (empty($nome)) {
                $erros[] = "O campo nome é obrigatório.";
            }

            if (empty($grupo_id)) {
                $erros[] = "Você deve selecionar um grupo.";
            }

            if (!is_numeric($preco) || $preco <= 0) {
                $erros[] = "O preço deve ser um número maior que zero.";
            }

            // Se não houver erros, insere no banco
            if (empty($erros)) {
                $sql = "INSERT INTO produtos (nome, descricao, grupo_id, preco, ativo)
                        VALUES (:nome, :descricao, :grupo_id, :preco, :ativo)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':nome', $nome);
                $stmt->bindValue(':descricao', $descricao);
                $stmt->bindValue(':grupo_id', (int)$grupo_id);
                $stmt->bindValue(':preco', (float)$preco);
                $stmt->bindValue(':ativo', $ativo);
                $stmt->execute();

                $success = "Produto cadastrado com sucesso!";

                // Limpa os campos após sucesso
                $nome = $descricao = $grupo_id = $preco = '';
                $ativo = 1;
            }
        }

        // Consulta com filtro de pesquisa
        if (!empty($_GET['search'])) {
            $data = "%" . $_GET['search'] . "%";
            $sql = "SELECT p.*, g.nome as grupo_nome FROM produtos p
                    LEFT JOIN grupos g ON p.grupo_id = g.id
                    WHERE unaccent(p.nome) ILIKE unaccent(:data)
                    OR unaccent(p.descricao) ILIKE unaccent(:data)
                    OR CAST(p.id AS TEXT) ILIKE :data
                    ORDER BY p.id ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':data', $data, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $sql = "SELECT p.*, g.nome as grupo_nome FROM produtos p
                    LEFT JOIN grupos g ON p.grupo_id = g.id
                    ORDER BY p.id ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        }
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para obter grupos
        $sql = "SELECT id, nome FROM grupos ORDER BY id";
        $stmt = $this->pdo->query($sql);
        $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar a view passando todas as variáveis necessárias
        echo $this->view->render("produtos/index", [
            "titulo" => $titulo,
            "produtos" => $produtos,
            "grupos" => $grupos,
            "erros" => $erros,
            "success" => $success,
            "nome" => $nome,
            "descricao" => $descricao,
            "grupo_id" => $grupo_id,
            "preco" => $preco,
            "ativo" => $ativo
        ]);
    }

    // Resto dos métodos permanecem iguais...
    public function create(): void
    {
        // Buscar grupos para o formulário
        $sql = "SELECT id, nome FROM grupos ORDER BY nome";
        $stmt = $this->pdo->query($sql);
        $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $this->view->render("produtos/form", [
            "title" => "Administração - Produtos",
            "crumb" => "<a href='" . url("admin/produtos/create") . "'>Adicionar Produto</a>",
            "produtos" => (new Produtos())->find()->fetch(true),
            "grupos" => $grupos
        ]);
    }

    public function update($data): void
    {
        // Verifica se o ID foi passado
        if (!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])) {
            $_SESSION["messageError"] = "ID do produto não informado ou inválido.";
            header('Location: ' . url("admin/produtos"));
            return;
        }

        // Busca o produto pelo ID
        $sql = "SELECT * FROM produtos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int)$data['id']);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o produto foi encontrado
        if (!$produto) {
            $_SESSION["messageError"] = "Produto não encontrado.";
            header('Location: ' . url("admin/produtos"));
            return;
        }

        // Buscar grupos para o formulário
        $sql = "SELECT id, nome FROM grupos ORDER BY nome";
        $stmt = $this->pdo->query($sql);
        $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $this->view->render("produtos/form", [
            "title" => "Administração - Produtos",
            "crumb" => "<a href='" . url("admin/produtos/" . $data['id']) . "'>Atualizar Produto</a>",
            "grupos" => $grupos,
            "nome" => $produto['nome'],
            "descricao" => $produto['descricao'],
            "grupo_id" => $produto['grupo_id'],
            "preco" => $produto['preco'],
            "ativo" => $produto['ativo'],
            "produto_id" => $produto['id'],
            "erros" => []
        ]);
    }

    public function save($data): void
    {
        try {
            if ($data["id"])
                $dataDB = (new Produtos())->findById($data["id"]);
            else
                $dataDB = new Produtos();

            // $dataDB->updated_by = $this->grupos->id;
            // $dataDB->updated_at = Funcoes::dataHoraMysql();

            $dataDB->nome = $data["nome"];

            $dataDB->descricao = $data["descricao"];

            $dataDB->grupo_id = $data["grupo_id"];

            $dataDB->preco = $data["preco"];

            $dataDB->ativo = $data["ativo"];

            $inicio = false;

            if ($dataDB->save()) {
                $_SESSION["messageSuccess"] = "Registro salvo com sucesso.";
                $inicio = true;
            } else
                $_SESSION["messageError"] = "N&atilde;o foi poss&iacute;vel salvar o registro. {$dataDB->fail}";
        } catch (Exception $exception) {
            $_SESSION["messageError"] = "Erro ao processar requisi&ccedil;&atilde;o : " . $exception->getMessage();
        }

        if ($inicio)
            header('Location: ' . url("admin/produtos"));
        else
            $this->retorno($data);
    }

    // private function showFormWithErrors($data, $erros)
    // {
    //     // Buscar grupos para o formulário
    //     $sql = "SELECT id, nome FROM grupos ORDER BY nome";
    //     $stmt = $this->pdo->query($sql);
    //     $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     echo $this->view->render("produtos/form", [
    //         "title" => "Administração - Produtos",
    //         "crumb" => isset($data['id']) ? "<a href='" . url("admin/produtos/" . $data['id']) . "'>Atualizar Produto</a>" : "<a href='" . url("admin/produtos/create") . "'>Adicionar Produto</a>",
    //         "grupos" => $grupos,
    //         "nome" => $data['nome'] ?? "",
    //         "descricao" => $data['descricao'] ?? "",
    //         "grupo_id" => $data['grupo_id'] ?? "",
    //         "preco" => $data['preco'] ?? "",
    //         "ativo" => isset($data['ativo']) ? 1 : 0,
    //         "produto_id" => $data['id'] ?? null,
    //         "erros" => $erros
    //     ]);
    // }

    private function retorno(array $data)
    {
        if ($data["id"]) {
            $this->update($data);
        } else {
            $this->create();
        }
        die();
    }

    public function destroy($data): void
    {
        try {
            // Verificar se o ID foi passado
            if (!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])) {
                $_SESSION["messageError"] = "ID do produto não informado ou inválido.";
                header('Location: ' . url("admin/produtos"));
                return;
            }

            // Verificar se o produto existe
            $sql = "SELECT id FROM produtos WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', (int)$data['id']);
            $stmt->execute();
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                $_SESSION["messageError"] = "Produto não encontrado.";
            } else {
                // Excluir o produto
                $sql = "DELETE FROM produtos WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':id', (int)$data['id']);

                if ($stmt->execute()) {
                    $_SESSION["messageSuccess"] = "Produto excluído com sucesso!";
                } else {
                    $_SESSION["messageError"] = "Erro ao excluir o produto.";
                }
            }
        } catch (Exception $exception) {
            $_SESSION["messageError"] = "Erro ao processar requisição: " . $exception->getMessage();
        }

        header('Location: ' . url("admin/produtos"));
    }
}
