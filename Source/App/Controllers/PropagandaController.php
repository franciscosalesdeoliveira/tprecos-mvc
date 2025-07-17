<?php

namespace Source\App\Controllers;

use Exception;
use PDO;
use PDOException;
use Source\App\Models\Propagandas;

class PropagandaController extends BaseControllerAdmin
{
    private $propagandaModel;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = new PDO("pgsql:host=localhost;dbname=tprecos", "postgres", "admin");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->propagandaModel = new Propagandas($this->pdo);
    }

    public function index()
    {
        // Debug: verificar se chegou no método
        error_log("PropagandaController::index() chamado");

        $mensagem = null;
        $tipoMensagem = null;
        $propagandaEdicao = null;



        // Buscar todas as propagandas
        try {
            $propagandas = $this->propagandaModel->getAllPropagandas();
        } catch (Exception $e) {
            $mensagem = $e->getMessage();
            $tipoMensagem = "danger";
            $propagandas = [];
        }

        // Preparar dados para a view
        $uploadDir = 'uploads/propagandas/';

        // Renderizar view
        try {
            echo  $this->view->render('propaganda/index', [
                'titulo' => 'Gerenciar Propagandas',
                'mensagem' => $mensagem,
                'tipoMensagem' => $tipoMensagem,
                'propagandas' => $propagandas,
                'propagandaEdicao' => $propagandaEdicao,
                'uploadDir' => $uploadDir,
                'propagandaModel' => $this->propagandaModel,
                'getImagemUrl' => [$this->propagandaModel, 'getImagemUrl']
            ]);
            error_log("View renderizada com sucesso");
        } catch (Exception $e) {
            error_log("Erro ao renderizar view: " . $e->getMessage());
            echo "Erro ao carregar a página: " . $e->getMessage();
        }
    }

    public function edit($data): void
    {
        // Verifica se o ID foi passado e é válido
        if (!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])) {
            $_SESSION["messageError"] = "ID da propaganda não informado ou inválido.";
            header('Location: ' . url("admin/propagandas"));
            return;
        }

        // Buscar propaganda pelo ID
        $sql = "SELECT * FROM propagandas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int)$data['id']);
        $stmt->execute();
        $propaganda = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se a propaganda existe
        if (!$propaganda) {
            $_SESSION["messageError"] = "Propaganda não encontrada.";
            header('Location: ' . url("admin/propagandas"));
            return;
        }

        echo $this->view->render("propaganda/form", [
            "title" => "Editar Propaganda",
            "crumb" => "<a href='" . url("admin/propagandas/{$data['id']}/edit") . "'>Atualizar Propaganda</a>",
            "dataDB" => $propaganda,
            "erros" => []
        ]);
    }

    private function processarDadosFormulario()
    {
        $dados = [
            'titulo' => htmlspecialchars($_POST['titulo']),
            'descricao' => htmlspecialchars($_POST['descricao']),
            'ordem' => isset($_POST['ordem']) ? intval($_POST['ordem']) : 0,
            'ativo' => isset($_POST['ativo']) ? 'S' : 'N',
            'tipo_imagem' => $_POST['tipo_imagem'] ?? 'local'
        ];

        // Processar imagem baseado no tipo
        if ($dados['tipo_imagem'] == 'url') {
            $urlImagem = trim($_POST['url_imagem'] ?? '');
            $dados['imagem'] = $this->propagandaModel->validarUrl($urlImagem);
        } else {
            // Verificar se há arquivo enviado
            $temArquivo = isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE;

            if ($temArquivo) {
                $dados['imagem'] = $this->propagandaModel->processarUpload($_FILES['imagem']);
            } elseif (!isset($_POST['id'])) {
                // Se for nova propaganda e não tem imagem
                throw new Exception("É necessário enviar uma imagem para a propaganda.");
            }
        }

        return $dados;
    }

    private function criarPropaganda($dados)
    {
        return $this->propagandaModel->createPropaganda($dados);
    }

    public function update($data)
    {
        $id = $data['id'] ?? null;

        if (!$id) {
            $_SESSION['messageError'] = "ID inválido para atualização.";
            header("Location: " . url("admin/propagandas"));
            return;
        }

        // Combinar dados da rota com $_POST
        $dados = array_merge($data, $_POST);

        // Verifica se imagem deve ser atualizada
        $atualizarImagem = false;

        if (($dados['tipo_imagem'] ?? '') === 'url' && !empty($dados['imagem'])) {
            $atualizarImagem = true;
        } elseif (($dados['tipo_imagem'] ?? '') === 'local' && isset($dados['imagem'])) {
            // Verifica e remove imagem antiga
            $propagandaAntiga = $this->propagandaModel->getPropagandaById($id);
            if (
                $propagandaAntiga &&
                ($propagandaAntiga['tipo_imagem'] ?? 'local') === 'local' &&
                $propagandaAntiga['imagem'] &&
                file_exists('uploads/propagandas/' . $propagandaAntiga['imagem'])
            ) {
                unlink('uploads/propagandas/' . $propagandaAntiga['imagem']);
            }
            $atualizarImagem = true;
        }

        // Atualiza no banco
        $this->propagandaModel->updatePropaganda($id, $dados, $atualizarImagem);

        $_SESSION['messageSuccess'] = "Propaganda atualizada com sucesso!";
        header("Location: " . url("admin/propagandas"));
    }


    public function create()
    {
        // Renderizar formulário de criação se necessário
        echo $this->view->render('propaganda/form', [
            'titulo' => 'Nova Propaganda',
            'crumb' => "<a href='" . url("admin/propagandas/create") . "'>Gerenciar Propagandas</a>",
            'propagandaEdicao' => null
        ]);
    }

    public function getPropagandasAtivas()
    {
        // Método para API ou uso externo
        try {
            return $this->propagandaModel->getPropagandasAtivas();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }



    public function save($data = null)
    {
        try {
            // Se não há dados passados, pegar do POST
            if ($data === null) {
                $data = $_POST;
            }

            // CORREÇÃO: Normalizar campo 'ativo' - checkbox envia valor quando marcado, null quando não marcado
            $ativo = isset($data["ativo"]) ? 'S' : 'N';

            // Tratamento do upload da imagem
            $imagem = null;

            if (!empty($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $pastaDestino = "uploads/propagandas";
                if (!is_dir($pastaDestino)) {
                    mkdir($pastaDestino, 0755, true);
                }

                $nomeOriginal = $_FILES['imagem']['name'];
                $nomeTemp = $_FILES['imagem']['tmp_name'];
                $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                $nomeFinal = uniqid("banner_") . "." . $extensao;

                if (move_uploaded_file($nomeTemp, $pastaDestino . "/" . $nomeFinal)) {
                    $imagem = "uploads/propagandas/" . $nomeFinal;
                }
            }
            // Processa imagem se tipo_imagem for URL
            if (($data["tipo_imagem"] ?? '') === 'url' && !empty($data["url_imagem"])) {
                $imagem = trim($data["url_imagem"]);
            }


            // Verifica se é edição
            if (isset($data["id"]) && !empty($data["id"])) {
                $dataDB = $this->propagandaModel->getPropagandaById($data["id"]);
                if (!$dataDB) {
                    throw new Exception('Registro não encontrado para atualização.');
                }

                // Atualiza apenas se nova imagem foi enviada
                $dadosProcessados = [
                    'titulo'       => trim($data["titulo"] ?? ''),
                    'descricao'    => trim($data["descricao"] ?? ''),
                    'ordem'        => (int)($data["ordem"] ?? 0),
                    'ativo'        => $ativo,
                    'tipo_imagem'  => $data["tipo_imagem"] ?? 'local'
                ];

                if ($imagem) {
                    $dadosProcessados['imagem'] = $imagem;
                }

                $this->propagandaModel->updatePropaganda($data["id"], $dadosProcessados);
                $mensagem = 'Propaganda atualizada com sucesso!';
                $tipoMensagem = 'success';
            } else {
                // Criação de nova propaganda
                $dadosProcessados = [
                    'titulo'       => trim($data["titulo"] ?? ''),
                    'descricao'    => trim($data["descricao"] ?? ''),
                    'ordem'        => (int)($data["ordem"] ?? 0),
                    'ativo'        => $ativo,
                    'tipo_imagem'  => $data["tipo_imagem"] ?? 'local',
                    'imagem'       => $imagem
                ];

                $this->propagandaModel->createPropaganda($dadosProcessados);
                $mensagem = 'Propaganda criada com sucesso!';
                $tipoMensagem = 'success';
            }

            // Redirecionar para a página de listagem com mensagem de sucesso
            header("Location: " . url("admin/propagandas") . "?success=" . urlencode($mensagem));
            exit();
        } catch (Exception $e) {
            // Em caso de erro, redirecionar com mensagem de erro
            header("Location: " . url("admin/propagandas") . "?error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    public function destroy($data)
    {
        try {
            // Verifica se o ID foi passado e é válido
            if (!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])) {
                $_SESSION["messageError"] = "ID da propaganda não informado ou inválido.";
                header('Location: ' . url("admin/propagandas"));
                return;
            }

            // Verificar se a propaganda existe
            $sql = "SELECT id FROM propagandas WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', (int)$data['id']);
            $stmt->execute();
            $propaganda = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$propaganda) {
                $_SESSION["messageError"] = "Propaganda não encontrada.";
            } else {
                // Excluir a propaganda
                $sql = "DELETE FROM propagandas WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':id', (int)$data['id']);

                if ($stmt->execute()) {
                    $_SESSION["messageSuccess"] = "Propaganda excluída com sucesso!";
                } else {
                    $_SESSION["messageError"] = "Erro ao excluir a propaganda.";
                }
            }
        } catch (Exception $exception) {
            $_SESSION["messageError"] = "Erro ao processar requisição: " . $exception->getMessage();
        }

        header('Location: ' . url("admin/propagandas"));
    }
}
