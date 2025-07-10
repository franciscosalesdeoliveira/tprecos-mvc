<?php

namespace Source\App\Controllers;

use League\Plates\Engine;
use PDO;

abstract class BaseController
{
    protected $view;

    protected $pdo;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE)
            session_start();
        $dir = $dir ?? dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "web";
        $this->view = new Engine($dir, "php");

        $this->pdo = new PDO("pgsql:host=localhost;dbname=tprecos", "postgres", "admin");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
