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
        $titulo = "Página Inicial";

        echo $this->view->render("index", [
            "title" => SITE
        ]);
    }
}
