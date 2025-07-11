<?php

namespace Source\App\Controllers;

use League\Plates\Engine;
use PDO;
use Source\Models\User;

class AuthWebController extends BaseController // ← MUDANÇA: herda de BaseController, não BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login($data): void
    {
        // Criar conexão PDO (já que não herda mais de BaseControllerAdmin)
        $this->pdo = new PDO("pgsql:host=localhost;dbname=tprecos", "postgres", "admin");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar se a sessão não expirou
        $tempo_limite_sessao = 8 * 60 * 60; // 8 horas em segundos
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $tempo_limite_sessao)) {
            // Sessão expirada, limpar e redirecionar
            session_unset();
            session_destroy();
            header("Location: " . url("?msg=sessao_expirada"));
            exit;
        }

        // Se já está logado, redirecionar para admin
        if (!empty($_SESSION['user_id'])) {
            header("Location: " . url("admin/index"));
            exit;
        }

        $error_message = '';
        $success_message = '';
        $username = '';

        // Verificar se é POST antes de processar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error_message = "Por favor, preencha todos os campos.";
            } else {
                try {
                    // Buscar usuário no banco (pode ser email ou nome)
                    $sql = "SELECT u.id, u.uuid, u.nome, u.email, u.senha, u.ativo, u.empresaid, e.razao_social 
                        FROM usuarios u 
                        JOIN empresas e ON u.empresaid = e.id 
                        WHERE (u.email = :username OR u.nome = :username) 
                        AND u.apagado_em IS NULL 
                        AND u.ativo = 'S' 
                        AND e.ativo = 'S'";

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();

                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && password_verify($password, $user['senha'])) {
                        // Login bem-sucedido
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_uuid'] = $user['uuid'];
                        $_SESSION['user_name'] = $user['nome'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['empresa_id'] = $user['empresaid'];
                        $_SESSION['empresa_nome'] = $user['razao_social'];
                        $_SESSION['login_time'] = time();

                        // Atualizar último acesso
                        try {
                            $updateStmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_acesso = NOW(), updated_at = NOW() WHERE id = ?");
                            $updateStmt->execute([$user['id']]);
                        } catch (\PDOException $e) {
                            error_log("Erro ao atualizar ultimo acesso: " . $e->getMessage());
                        }

                        // Redirecionar para admin
                        header("Location: " . url("admin/"));
                        exit;
                    } else {
                        $error_message = "Usuário ou senha incorretos.";
                    }
                } catch (\PDOException $e) {
                    $error_message = "Erro ao processar login. Tente novamente.";
                    error_log("Erro de login: " . $e->getMessage());
                }
            }
        }

        // Renderizar a view de login com as mensagens
        echo $this->view->render("login", [
            "title" => SITE,
            "error_message" => $error_message,
            "success_message" => $success_message,
            "username" => $username
        ]);
    }

    public function logout($data): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: " . url(""));
        exit;
    }
}
