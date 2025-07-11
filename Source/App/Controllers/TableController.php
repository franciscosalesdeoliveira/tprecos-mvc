<?php

namespace Source\App\Controllers;

class TableController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        // Configurações da tabela
        $limiteGrupo = isset($_GET['limite']) && is_numeric($_GET['limite']) ? (int)$_GET['limite'] : 5;
        $tempoSlide = isset($_GET['tempo']) && is_numeric($_GET['tempo']) ? (int)$_GET['tempo'] * 1000 : 5000;
        $tempoExtraPorProduto = 500; // Adiciona 0.5s por produto
        $tempoRolagem = isset($_GET['rolagem']) && is_numeric($_GET['rolagem']) ? (int)$_GET['rolagem'] : 25; // Tempo de rolagem em segundos
        $grupoSelecionado = isset($_GET['grupo']) ? $_GET['grupo'] : 'todos';

        // Tempo de propaganda - AJUSTADO
        $tempo_propagandas = isset($_GET['tempo_propagandas']) && is_numeric($_GET['tempo_propagandas'])
            ? (int)$_GET['tempo_propagandas'] * 1000
            : 5000; // Converte para milissegundos

        // Configuração de atualização automática (em minutos)
        $atualizacao_auto = isset($_GET['atualizacao_auto']) && is_numeric($_GET['atualizacao_auto']) ? (int)$_GET['atualizacao_auto'] : 10;

        // Configurações de propagandas
        $propagandas_ativas = isset($_GET['propagandas_ativas']) ? (int)$_GET['propagandas_ativas'] : 1;
        if ($propagandas_ativas !== 0) { // Considera qualquer valor diferente de 0 como ativo
            $propagandas_ativas = 1;
        }

        // Definir estilo de tema (pode vir do banco de dados ou configurações)
        $tema = isset($_GET['tema']) ? $_GET['tema'] : 'padrao';
        $temas = [
            'padrao' => [
                'background' => 'bg-dark',
                'text' => 'text-white',
                'header_bg' => 'bg-primary',
                'table_header' => 'table-primary'
            ],
            'supermercado' => [
                'background' => 'bg-success bg-gradient',
                'text' => 'text-white',
                'header_bg' => 'bg-success',
                'table_header' => 'table-success'
            ],
            'padaria' => [
                'background' => 'bg-warning bg-gradient',
                'text' => 'text-dark',
                'header_bg' => 'bg-warning',
                'table_header' => 'table-warning'
            ]
        ];

        // Usar o tema selecionado ou o padrão se não existir
        $estiloAtual = isset($temas[$tema]) ? $temas[$tema] : $temas['padrao'];


        // Renderiza a view de tabela de preços
        echo $this->view->render("tabela_precos/index", [
            "title" => "Tabela de Preços - Admin",
            "dataDB" => [], // Aqui você pode passar os dados necessários para a view
        ]);
    }

    // Outros métodos relacionados à tabela de preços podem ser adicionados aqui
}
