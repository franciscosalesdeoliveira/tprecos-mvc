<?php

namespace Source\App\Controllers;

use Source\App\Models\Grupos;

class ConfigController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $grupo_selecionado = $_GET['grupo'] ?? 'todos';
        $tempo = (int)($_GET['tempo'] ?? 10);
        $limite = (int)($_GET['limite'] ?? 5);
        $tema = $_GET['tema'] ?? 'padrao';
        $propagandas_ativas = isset($_GET['propagandas_ativas']) && $_GET['propagandas_ativas'] == '1' ? 1 : 0;
        $tempo_propagandas = (int)($_GET['tempo_propagandas'] ?? 5);
        $atualizacao_auto = (int)($_GET['atualizacao_auto'] ?? 10);

        // Temas disponíveis
        $temas = [
            'padrao' => 'Padrão <br> (Azul)',
            'supermercado' => 'Supermercado <br> (Verde)',
            'padaria' => 'Padaria <br> (Amarelo)'
        ];

        $opcoes_atualizacao = [
            1 => '1 minuto',
            5 => '5 minutos',
            10 => '10 minutos',
            15 => '15 minutos',
            30 => '30 minutos',
            60 => '1 hora',
            0 => 'Desativar'
        ];

        $grupos = Grupos::listaParaSelect();

        echo $this->view->render("configuracoes/index", [
            "grupo_selecionado" => $grupo_selecionado,
            "tempo" => $tempo,
            "limite" => $limite,
            "tema" => $tema,
            "propagandas_ativas" => $propagandas_ativas,
            "tempo_propagandas" => $tempo_propagandas,
            "atualizacao_auto" => $atualizacao_auto,
            "temas" => $temas,
            "opcoes_atualizacao" => $opcoes_atualizacao,
            "grupos" => $grupos
        ]);
    }
}
