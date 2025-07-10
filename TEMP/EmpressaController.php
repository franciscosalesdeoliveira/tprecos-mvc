<?php

namespace Source\App\Controllers;

use League\Plates\Engine;

class EmpressaController
{
    private Engine $view;

    public function __construct()
    {
        var_dump("passei aqui");
        $this->view = new Engine(__DIR__ . "/../../views/admin", "php");
    }


    public function index($data): void
    {
        echo $this->view->render("cadastro_empresa", [
            "title" => SITE
        ]);
    }
}
