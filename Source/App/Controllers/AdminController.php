<?php

namespace Source\App\Controllers;

use League\Plates\Engine;
use PDO;

class AdminController extends BaseControllerAdmin
{


    public function __construct()
    {
        parent::__construct();
    }


    public function index($data): void
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_uuid'])) {
            // Se não estiver logado, redirecionar para o login
            header("Location: " . url(""));
            exit;
        }

        // Verificar se a sessão não expirou (opcional - definir tempo limite)
        $tempo_limite_sessao = 8 * 60 * 60; // 8 horas em segundos
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $tempo_limite_sessao)) {
            // Sessão expirada, limpar e redirecionar
            session_unset();
            session_destroy();
            header("Location: login.php?msg=sessao_expirada");
            exit;
        }

        // Atualizar o tempo da sessão (renovar automaticamente)
        $_SESSION['login_time'] = time();

        $titulo = "Página Inicial";


        echo $this->view->render("index", [
            "title" => SITE
        ]);
    }


    public function contato($data): void
    {
        echo $this->view->render("contato", [
            "title" => SITE
        ]);
    }
}
