<?php

namespace Source\App\Controllers;

use League\Plates\Engine;
use PDO;
use Source\Models\User;

class WebController extends BaseController
{


    public function __construct()
    {
        parent::__construct();
    }

    public function login($data): void
    {
        echo $this->view->render("login", [
            "title" => SITE
        ]);
    }



    public function negado($data): void
    {
        echo $this->view->render("sempermissao", [
            "title" => "Acesso Negado"
        ]);
    }

    public function error(array $data): void
    {
        echo $this->view->render("error", [
            "title" => "Error {$data['errcode']} | " . SITE,
            "error" => $data["errcode"]
        ]);
    }
}
