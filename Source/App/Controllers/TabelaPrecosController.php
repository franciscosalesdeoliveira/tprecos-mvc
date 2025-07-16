<?php

namespace Source\App\Controllers;

use Source\App\Models\Produto;
use Source\App\Models\Grupo;
use Source\App\Models\Propaganda;
use Source\App\Models\Connect;
use PDO;
use PDOException;
use DateTime;

class TabelaPrecosController extends BaseControllerAdmin
{


    public function __construct()
    {
        parent::__construct();
        // $this->pdo = Connect::getInstance();
    }

    public function index(): void
    {

        // Verificar se é uma atualização via AJAX
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        // Configurações da tabela vindas dos parâmetros GET
        $config = $this->getConfiguracoes();


        try {
            // Buscar produtos
            $produtos = $this->buscarProdutos($config['grupoSelecionado']);


            // Buscar propagandas se ativas
            $propagandas = [];
            if ($config['propagandas_ativas']) {
                $propagandas = $this->buscarPropagandas();
            }

            // Organizar produtos por grupo
            $grupos = $this->organizarProdutosPorGrupo($produtos);

            // Definir se deve mostrar carrossel
            $mostrarCarrossel = ($config['grupoSelecionado'] === 'todos') || !empty($propagandas);

            // Renderizar view
            echo $this->view->render("tabela_precos/index", [
                "title" => "Tabela de Preços",
                "config" => $config,
                "grupos" => $grupos,
                "propagandas" => $propagandas,
                "mostrarCarrossel" => $mostrarCarrossel,
                "isAjax" => $isAjax
            ]);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    private function getConfiguracoes(): array
    {
        $limiteGrupo = isset($_GET['limite']) && is_numeric($_GET['limite']) ?
            (int)$_GET['limite'] : 5;

        $tempoSlide = isset($_GET['tempo']) && is_numeric($_GET['tempo']) ?
            (int)$_GET['tempo'] * 1000 : 5000;

        $tempoExtraPorProduto = 500; // Adiciona 0.5s por produto

        $tempoRolagem = isset($_GET['rolagem']) && is_numeric($_GET['rolagem']) ?
            (int)$_GET['rolagem'] : 25;

        $grupoSelecionado = isset($_GET['grupo']) ? $_GET['grupo'] : 'todos';

        $tempo_propagandas = isset($_GET['tempo_propagandas']) && is_numeric($_GET['tempo_propagandas'])
            ? (int)$_GET['tempo_propagandas'] * 1000
            : 5000;

        $atualizacao_auto = isset($_GET['atualizacao_auto']) && is_numeric($_GET['atualizacao_auto']) ?
            (int)$_GET['atualizacao_auto'] : 10;

        $propagandas_ativas = isset($_GET['propagandas_ativas']) ?
            (int)$_GET['propagandas_ativas'] : 1;
        if ($propagandas_ativas !== 0) {
            $propagandas_ativas = 1;
        }

        $tema = isset($_GET['tema']) ? $_GET['tema'] : 'padrao';
        $estiloAtual = $this->getTema($tema);

        return [
            'limiteGrupo' => $limiteGrupo,
            'tempoSlide' => $tempoSlide,
            'tempoExtraPorProduto' => $tempoExtraPorProduto,
            'tempoRolagem' => $tempoRolagem,
            'grupoSelecionado' => $grupoSelecionado,
            'tempo_propagandas' => $tempo_propagandas,
            'atualizacao_auto' => $atualizacao_auto,
            'propagandas_ativas' => $propagandas_ativas,
            'tema' => $tema,
            'estiloAtual' => $estiloAtual
        ];
    }

    private function getTema(string $tema): array
    {
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

        return isset($temas[$tema]) ? $temas[$tema] : $temas['padrao'];
    }

    private function buscarProdutos(string $grupoSelecionado): array
    {
        $dbType = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        // Verificar se as colunas 'ativo' existem
        $temColunaAtivoProdutos = $this->verificarColunaAtivo('produtos');
        $temColunaAtivoGrupos = $this->verificarColunaAtivo('grupos');
        $temColunaUpdatedAt = $this->verificarColuna('produtos', 'updated_at');

        // Montar SQL
        $sql = "SELECT p.nome as produto, p.preco, g.nome as grupo, 
                      p.id as produto_id, g.id as grupo_id" .
            ($temColunaUpdatedAt ? ", p.updated_at as ultima_atualizacao" : ", NULL as ultima_atualizacao") . "
               FROM produtos p
               JOIN grupos g ON p.grupo_id = g.id
               WHERE 1=1";

        // Adicionar filtros de ativo
        if ($temColunaAtivoGrupos) {
            $sql .= " AND g.ativo = 'S'";
        }
        if ($temColunaAtivoProdutos) {
            $sql .= " AND p.ativo = 'S'";
        }

        // Filtro de grupo específico
        if ($grupoSelecionado !== 'todos') {
            $sql .= " AND g.id = :grupo_id";
        }

        $sql .= " ORDER BY g.nome, p.nome";

        $stmt = $this->pdo->prepare($sql);

        if ($grupoSelecionado !== 'todos') {
            $stmt->bindParam(':grupo_id', $grupoSelecionado, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buscarPropagandas(): array
    {
        $dbType = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        $sql = "SELECT id, titulo, descricao, imagem, ordem 
                FROM propagandas 
                WHERE ativo = 'S' 
                ORDER BY ordem, id";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function organizarProdutosPorGrupo(array $produtos): array
    {
        $grupos = [];
        foreach ($produtos as $produto) {
            $grupos[$produto['grupo']][] = $produto;
        }
        return $grupos;
    }

    private function verificarColunaAtivo(string $tabela): bool
    {
        return $this->verificarColuna($tabela, 'ativo');
    }

    private function verificarColuna(string $tabela, string $coluna): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$tabela} LIMIT 1");
            $stmt->execute();

            $colunas = [];
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $colMeta = $stmt->getColumnMeta($i);
                $colunas[] = strtolower($colMeta['name']);
            }

            return in_array($coluna, $colunas);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function handleError(PDOException $e): void
    {
        $isDev = false; // Configurar conforme ambiente

        if ($isDev) {
            echo '<div class="alert alert-danger m-4">
                    <h4>Erro ao carregar a tabela de preços:</h4>
                    <p>' . htmlspecialchars($e->getMessage()) . '</p>
                    <p>Arquivo: ' . htmlspecialchars($e->getFile()) . ' (linha ' . $e->getLine() . ')</p>
                </div>';
        } else {
            echo '<div class="alert alert-danger m-4">
                    Ocorreu um erro ao carregar a tabela de preços. Por favor, tente novamente mais tarde.
                </div>';
        }
    }

    /**
     * Endpoint para recarregar conteúdo via AJAX
     */
    public function reload(): void
    {
        header('Content-Type: application/json');
        header('X-Requested-With: XMLHttpRequest');

        $this->index();
    }
}
