<?php

namespace Source\App\Controllers;

use League\Plates\Engine;
use PDO;

abstract class BaseControllerAdmin
{
    protected $view;
    protected $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        // Verificar se a sessão não expirou
        $tempo_limite_sessao = 8 * 60 * 60; // 8 horas em segundos
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $tempo_limite_sessao)) {
            session_unset();
            session_destroy();
            header("Location: login.php?msg=sessao_expirada");
            exit;
        }

        // Atualizar o tempo da sessão
        $_SESSION['login_time'] = time();


        // ✅ Proteção: só acessa se estiver logado
        if (empty($_SESSION['user_id'])) {
            header("Location: " . url("")); // redireciona para o login
            exit;
        }

        $dir = dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "admin";
        $this->view = new Engine($dir, "php");

        $this->pdo = new PDO("pgsql:host=localhost;dbname=tprecos", "postgres", "admin");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
