<?php

namespace Source\App\Controllers;


use PDO;
use PDOException;
use Source\App\helpers\Connection;

class ImportController extends BaseControllerAdmin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        try {
            $pdo = \Source\helpers\Connection::getPDO(); // conexão centralizada

            $stmt = $pdo->query("SELECT id, nome FROM grupos ORDER BY nome");
            $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo $this->view->render("excel/index", [
                "title" => "Importar Produtos CSV",
                "grupos" => $grupos
            ]);
        } catch (PDOException $e) {
            echo "Erro ao buscar grupos: " . $e->getMessage();
        }
    }


    public function importar()
    {
        // Lógica para importar o CSV

    }



    public function processar(): void
    {

        // Verifica se é uma requisição AJAX para atualização de progresso
        if (isset($_GET['check_progress'])) {
            // Retorna o progresso atual se existir na sessão
            $response = [
                'status' => isset($_SESSION['import_status']) ? $_SESSION['import_status'] : 'waiting',
                'progress' => isset($_SESSION['import_progress']) ? $_SESSION['import_progress'] : 0,
                'total_linhas' => isset($_SESSION['total_linhas']) ? $_SESSION['total_linhas'] : 0,
                'linhas_processadas' => isset($_SESSION['linhas_processadas']) ? $_SESSION['linhas_processadas'] : 0,
                'linhas_importadas' => isset($_SESSION['linhas_importadas']) ? $_SESSION['linhas_importadas'] : 0,
                'linhas_falha' => isset($_SESSION['linhas_falha']) ? $_SESSION['linhas_falha'] : 0
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Verifica se é uma solicitação de conclusão (usuário clicou em "Concluir")
        if (isset($_GET['concluir'])) {
            // Limpa os dados da sessão
            unset($_SESSION['import_status']);
            unset($_SESSION['import_progress']);
            unset($_SESSION['total_linhas']);
            unset($_SESSION['linhas_processadas']);
            unset($_SESSION['linhas_importadas']);
            unset($_SESSION['linhas_falha']);
            unset($_SESSION['produtos_nao_importados']);
            unset($_SESSION['info_grupo']);
            unset($_SESSION['tem_descricao']);
            unset($_SESSION['erro_mensagem']);

            // Redireciona para a página de importação usando o helper url()
            header('Location: ' . url('admin/excel'));
            exit;
        }

        // Se houver um POST, processe o arquivo
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
            // Inicializa variáveis na sessão
            $_SESSION['import_status'] = 'processing';
            $_SESSION['import_progress'] = 0;
            $_SESSION['linhas_importadas'] = 0;
            $_SESSION['linhas_falha'] = 0;
            $_SESSION['produtos_nao_importados'] = "";

            $arquivo = $_FILES['arquivo'];

            // Verifica se houve erro no upload
            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['import_status'] = 'error';
                $_SESSION['erro_mensagem'] = "Erro no upload do arquivo.";
                $this->renderProcessPage();
                return;
            }

            // Captura o grupo selecionado pelo usuário (se houver)
            $grupo_selecionado = isset($_POST['grupo_selecionado']) && !empty($_POST['grupo_selecionado']) ?
                (int)$_POST['grupo_selecionado'] : null;

            // Verifica se é um arquivo CSV
            if (
                $arquivo['type'] !== "text/csv" &&
                !str_ends_with(strtolower($arquivo['name']), '.csv')
            ) {
                $_SESSION['import_status'] = 'error';
                $_SESSION['erro_mensagem'] = "Arquivo deve ser um CSV válido.";
                $this->renderProcessPage();
                return;
            }

            $dados_arquivo = fopen($arquivo['tmp_name'], "r");

            if (!$dados_arquivo) {
                $_SESSION['import_status'] = 'error';
                $_SESSION['erro_mensagem'] = "Não foi possível abrir o arquivo.";
                $this->renderProcessPage();
                return;
            }

            try {
                // Detecta o delimitador do CSV
                $delimitador = $this->detectarDelimitador($arquivo['tmp_name']);

                // Conta o número total de linhas para a barra de progresso
                $total_linhas = 0;
                while (fgetcsv($dados_arquivo, 1000, $delimitador)) {
                    $total_linhas++;
                }
                $_SESSION['total_linhas'] = $total_linhas - 1; // Desconta a linha de cabeçalho

                // Reseta o ponteiro do arquivo
                rewind($dados_arquivo);

                // Pular a primeira linha (cabeçalho)
                $primeira_linha = true;
                $tem_descricao = false;
                $indice_grupo_id = 2;
                $indice_preco = 3;
                $_SESSION['linhas_processadas'] = 0;

                while ($linha = fgetcsv($dados_arquivo, 1000, $delimitador)) {
                    if ($primeira_linha) {
                        // Debug: mostra como a linha foi separada
                        error_log("Primeira linha (cabeçalho): " . print_r($linha, true));

                        // Verifica a estrutura do cabeçalho para determinar se há coluna de descrição
                        $tem_descricao = false;
                        for ($i = 0; $i < count($linha); $i++) {
                            $cabecalho = mb_strtolower(trim($linha[$i]));
                            if ($cabecalho === 'descricao' || $cabecalho === 'descrição' || $cabecalho === 'descr') {
                                $tem_descricao = true;
                                $indice_descricao = $i;
                                // Ajusta os índices de grupo_id e preço
                                $indice_grupo_id = $i + 1;
                                $indice_preco = $i + 2;
                                break;
                            }
                        }
                        $_SESSION['tem_descricao'] = $tem_descricao;
                        $primeira_linha = false;
                        continue; // Pula a primeira linha (cabeçalho)
                    }

                    // Debug: mostra como a linha foi separada
                    error_log("Linha processada: " . print_r($linha, true));

                    // Verifica se a linha foi corretamente separada
                    if (count($linha) < 4) {
                        $_SESSION['linhas_falha']++;
                        $_SESSION['produtos_nao_importados'] .= "Linha mal formatada: " . implode(',', $linha) . "<br>";
                        $_SESSION['linhas_processadas']++;
                        continue;
                    }

                    // Incrementa o contador de linhas processadas
                    $_SESSION['linhas_processadas']++;

                    // Atualiza o progresso
                    $_SESSION['import_progress'] = round(($_SESSION['linhas_processadas'] / $_SESSION['total_linhas']) * 100);

                    // Converte todos os campos para UTF-8 se necessário
                    $linha = $this->converterLinhaParaUTF8($linha);

                    // Extrai os campos básicos que sempre existem
                    $codigo = isset($linha[0]) ? trim($linha[0]) : null;
                    $nome = isset($linha[1]) ? trim($linha[1]) : null;

                    // Determina os valores de descrição, grupo_id e preço com base na estrutura detectada
                    if ($tem_descricao) {
                        // Se tiver coluna de descrição, usa os índices ajustados
                        $descricao = isset($linha[$indice_descricao]) && !empty(trim($linha[$indice_descricao])) ?
                            trim($linha[$indice_descricao]) : null;
                        $grupo_id_csv = isset($linha[$indice_grupo_id]) && !empty(trim($linha[$indice_grupo_id])) ?
                            (int)trim($linha[$indice_grupo_id]) : null;
                        $preco = isset($linha[$indice_preco]) ? str_replace(',', '.', trim($linha[$indice_preco])) : null;
                    } else {
                        // Se não tiver coluna de descrição (como no seu CSV original)
                        $descricao = null;
                        $grupo_id_csv = isset($linha[2]) && !empty(trim($linha[2])) ? (int)trim($linha[2]) : null;
                        $preco = isset($linha[3]) ? str_replace(',', '.', trim($linha[3])) : null;
                    }

                    // Prioriza o grupo selecionado pelo usuário, se existir
                    $grupo_id = $grupo_selecionado !== null ? $grupo_selecionado : $grupo_id_csv;

                    // Valida os campos obrigatórios
                    if (empty($codigo) || empty($nome)) {
                        $_SESSION['linhas_falha']++;
                        $_SESSION['produtos_nao_importados'] .= "Código ou Nome vazio - Linha: " . implode(',', $linha) . "<br>";
                        continue;
                    }

                    // Valida o preço
                    if ($preco === null || !is_numeric($preco)) {
                        $preco = 0.00; // Define um valor padrão para preco caso esteja ausente ou inválido
                    }

                    // Verifica se o campo 'ativo' existe
                    $checkColumnQuery = "SELECT column_name 
                           FROM information_schema.columns 
                           WHERE table_name='produtos' AND column_name='ativo'";
                    $checkStmt = $this->pdo->query($checkColumnQuery);
                    $columnExists = $checkStmt->fetchColumn();

                    // Prepara a query considerando se o campo 'ativo' existe
                    if ($columnExists) {
                        $query_produto = "INSERT INTO produtos (codigo, nome, descricao, grupo_id, preco, ativo) 
                            VALUES (:codigo, :nome, :descricao, :grupo_id, :preco, TRUE)";
                    } else {
                        $query_produto = "INSERT INTO produtos (codigo, nome, descricao, grupo_id, preco) 
                            VALUES (:codigo, :nome, :descricao, :grupo_id, :preco)";
                    }

                    $stmt = $this->pdo->prepare($query_produto);
                    $stmt->bindValue(':codigo', $codigo);
                    $stmt->bindValue(':nome', $nome);
                    $stmt->bindValue(':descricao', $descricao);
                    $stmt->bindValue(':grupo_id', $grupo_id);
                    $stmt->bindValue(':preco', (float)$preco); // Garante que seja tratado como float

                    try {
                        $stmt->execute();

                        // Verifica se a inserção foi bem-sucedida
                        if ($stmt->rowCount() > 0) {
                            $_SESSION['linhas_importadas']++;
                        } else {
                            $_SESSION['linhas_falha']++;
                            $_SESSION['produtos_nao_importados'] .= "ID: " . $codigo . " - Nome: " . $nome . "<br>";
                        }
                    } catch (PDOException $e) {
                        $_SESSION['linhas_falha']++;
                        $_SESSION['produtos_nao_importados'] .= "Erro ao importar - ID: " . $codigo . " - Nome: " . $nome . " - Erro: " . $e->getMessage() . "<br>";
                    }
                }

                // Informações sobre o grupo utilizado
                if ($grupo_selecionado !== null) {
                    // Busca o nome do grupo selecionado
                    $query_grupo = "SELECT nome FROM grupos WHERE id = :id";
                    $stmt_grupo = $this->pdo->prepare($query_grupo);
                    $stmt_grupo->bindValue(':id', $grupo_selecionado);
                    $stmt_grupo->execute();
                    $grupo = $stmt_grupo->fetch(PDO::FETCH_ASSOC);

                    if ($grupo) {
                        $_SESSION['info_grupo'] = "Todos os produtos foram importados para o grupo: " . $grupo['nome'] . " (ID: $grupo_selecionado)";
                    } else {
                        $_SESSION['info_grupo'] = "Grupo selecionado (ID: $grupo_selecionado) utilizado para todos os produtos";
                    }
                } else {
                    $_SESSION['info_grupo'] = "Utilizando os grupos definidos no arquivo CSV";
                }

                // Marca como concluído
                $_SESSION['import_status'] = 'completed';
                $_SESSION['import_progress'] = 100;

                fclose($dados_arquivo);
            } catch (\Exception $e) {
                $_SESSION['import_status'] = 'error';
                $_SESSION['erro_mensagem'] = "Erro durante o processamento: " . $e->getMessage();
                fclose($dados_arquivo);
            }
        }

        // Renderiza a página de processamento
        $this->renderProcessPage();
    }

    /**
     * Detecta o delimitador do CSV
     */
    private function detectarDelimitador($arquivo): string
    {
        $delimitadores = [',', ';', '\t', '|'];
        $handle = fopen($arquivo, 'r');

        if (!$handle) {
            return ','; // Padrão
        }

        // Lê as primeiras linhas para detectar o delimitador
        $primeira_linha = fgets($handle);
        fclose($handle);

        $contadores = [];
        foreach ($delimitadores as $delim) {
            $contadores[$delim] = substr_count($primeira_linha, $delim);
        }

        // Retorna o delimitador mais comum
        $delimitador_detectado = array_search(max($contadores), $contadores);

        error_log("Delimitador detectado: " . $delimitador_detectado);

        return $delimitador_detectado ?: ',';
    }

    /**
     * Renderiza a página de processamento
     */
    private function renderProcessPage(): void
    {
        $mostrar_resultado = isset($_SESSION['import_status']) &&
            ($_SESSION['import_status'] === 'completed' || $_SESSION['import_status'] === 'error');

        echo $this->view->render("excel/processar", [
            "title" => "Processando Importação",
            "mostrar_resultado" => $mostrar_resultado
        ]);
    }

    /**
     * Converte uma linha do CSV para UTF-8
     */
    private function converterLinhaParaUTF8($linha): array
    {
        $linha_convertida = [];

        foreach ($linha as $campo) {
            // Primeiro, tenta detectar a codificação
            $encoding = mb_detect_encoding($campo, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'CP1252'], true);

            if ($encoding && $encoding !== 'UTF-8') {
                // Converte para UTF-8
                $campo_convertido = mb_convert_encoding($campo, 'UTF-8', $encoding);
            } else if (!mb_check_encoding($campo, 'UTF-8')) {
                // Se não conseguiu detectar mas não está em UTF-8, assume ISO-8859-1
                $campo_convertido = mb_convert_encoding($campo, 'UTF-8', 'ISO-8859-1');
            } else {
                // Já está em UTF-8 válido
                $campo_convertido = $campo;
            }

            $linha_convertida[] = $campo_convertido;
        }

        return $linha_convertida;
    }

    /**
     * Função para converter o arquivo CSV para UTF-8 (método legado - mantido para compatibilidade)
     */
    private function converterParaUTF8(&$dados_arquivo): void
    {
        $dados_arquivo = mb_convert_encoding($dados_arquivo, 'UTF-8', 'ISO-8859-1');
    }
}
