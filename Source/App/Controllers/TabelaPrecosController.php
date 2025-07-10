<?php

namespace Source\App\Controllers;

class TabelaPrecosController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        // Renderiza a view de tabela de preços
        echo $this->view->render("tabela_precos/index", [
            "title" => "Tabela de Preços - Admin",
            "dataDB" => [], // Aqui você pode passar os dados necessários para a view
        ]);
    }

    // Outros métodos relacionados à tabela de preços podem ser adicionados aqui
} {
}
