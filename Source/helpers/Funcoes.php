<?php

namespace Source\App\helpers;

use DateTime;
use Exception;

class Funcoes
{

    // Função para inverter direção da ordenação
    public static function inverterDirecao($atual)
    {
        return $atual === 'ASC' ? 'DESC' : 'ASC';
    }

    // Função para criar URL de ordenação
    public static function urlOrdenacao($coluna, $ordem_atual, $direcao_atual)
    {
        $nova_direcao = ($coluna === $ordem_atual) ? self::inverterDirecao($direcao_atual) : 'ASC';
        $params = $_GET;
        $params['ordem'] = $coluna;
        $params['direcao'] = $nova_direcao;
        return '?' . http_build_query($params);
    }

    // Função para criar URL de paginação
    public static function urlPaginacao($pagina)
    {
        $params = $_GET;
        $params['pagina'] = $pagina;
        return '?' . http_build_query($params);
    }

    // Função para criar URL de filtro de status
    public static function urlFiltroStatus($status)
    {
        $params = $_GET;
        $params['status'] = $status;
        $params['pagina'] = 1; // Voltar para a primeira página ao mudar o filtro
        return '?' . http_build_query($params);
    }

    // Função para checar se um grupo está ativo
    public static function isGrupoAtivo($grupo)
    {
        // Tratar todas as representações possíveis de boolean true em PostgreSQL
        return ($grupo['ativo'] === true || $grupo['ativo'] === 't' ||
            $grupo['ativo'] === '1' || $grupo['ativo'] === 1 ||
            $grupo['ativo'] === 'true');
    }

    // Ícone para indicar a direção da ordenação
    public static function iconeOrdenacao($coluna, $ordem_atual, $direcao_atual)
    {
        if ($coluna !== $ordem_atual) {
            return '';
        }
        return ($direcao_atual === 'ASC') ? '↑' : '↓';
    }
}
