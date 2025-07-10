<?php
$this->layout("_theme");
$this->start("conteudo");
?>


<?php

use Source\App\helpers\Funcoes;


if (!empty($mensagem)): ?>
    <div class="alert-container">
        <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensagem) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Formulário para cadastrar grupos -->
<div class="form-container mt-3">
    <h2 class="text-center mb-4" style="font-size: 24px; font-weight: bold; color: black;">Cadastro de Grupos</h2>
    <form method="POST" class="row g-3 align-items-end">
        <script>
            const status = <?= json_encode($filtro_status) ?>;
        </script>
        <div class="col-md-6">
            <label class="form-label" style="font-weight: bold; color: black;" for="grupo">Nome do Grupo:</label>
            <input class="form-control" type="text" name="grupo" id="grupo" required>
        </div>

        <div class="col-md-2">
            <label class="form-label" style="font-weight: bold; color: black;">Status:</label>
            <select class="form-select" name="status" id="status">
                <option value="ativo" selected>Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-success flex-grow-1" type="submit">Cadastrar</button>
            <button class="btn btn-warning flex-grow-1" type="reset">Limpar</button>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-1"></i> Página Inicial
            </a>

        </div>
    </form>
</div>

<!-- Formulário de pesquisa -->
<div class="table-container">
    <div class="row mb-3 mt-3">
        <div class="col-md-6">
            <div class="input-group">
                <input class="form-control" type="search" id="pesquisar" placeholder="Pesquisar..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button onclick="searchData()" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="btn-group float-end" role="group">
                <a href="<?= Funcoes::urlFiltroStatus('todos') ?>" class="btn btn-outline-primary btn-sm <?= $filtro_status === 'todos' ? 'active' : '' ?>">Todos</a>
                <a href="<?= Funcoes::urlFiltroStatus('ativos') ?>" class="btn btn-outline-primary btn-sm <?= $filtro_status === 'ativos' ? 'active' : '' ?>">Ativos</a>
                <a href="<?= Funcoes::urlFiltroStatus('inativos') ?>" class="btn btn-outline-primary btn-sm <?= $filtro_status === 'inativos' ? 'active' : '' ?>">Inativos</a>
            </div>
        </div>
    </div>

    <!-- Listagem de Grupos -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center sorting-header" onclick="window.location.href='<?= Funcoes::urlOrdenacao('id', $ordem_coluna, $ordem_direcao) ?>'">
                        ID <?= Funcoes::iconeOrdenacao('id', $ordem_coluna, $ordem_direcao) ?>
                    </th>
                    <th class="text-center sorting-header" onclick="window.location.href='<?= Funcoes::urlOrdenacao('nome', $ordem_coluna, $ordem_direcao) ?>'">
                        Nome <?= Funcoes::iconeOrdenacao('nome', $ordem_coluna, $ordem_direcao) ?>
                    </th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($grupos) > 0): ?>
                    <?php foreach ($grupos as $grupo): ?>
                        <?php $ehAtivo = Funcoes::isGrupoAtivo($grupo); ?>
                        <tr class="<?= $ehAtivo ? '' : 'table-secondary' ?>">
                            <td class="text-center"><?= htmlspecialchars($grupo['id']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($grupo['nome']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $ehAtivo ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $ehAtivo ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-primary btn-sm" href="<?= url("admin/grupo/{$grupo['id']}") ?>">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>

                                    <?php
                                    $acaoCor = $ehAtivo ? 'warning' : 'success';
                                    $acaoIcone = $ehAtivo ? 'bi-toggle-off' : 'bi-toggle-on';
                                    $acaoTexto = $ehAtivo ? 'Inativar' : 'Ativar';
                                    $acaoParamtr = $ehAtivo ? 'inativar' : 'ativar';
                                    ?>
                                    <a class="btn btn-<?= $acaoCor ?> btn-sm"
                                        href="toggle_status_grupo.php?id=<?= $grupo['id'] ?>&acao=<?= $acaoParamtr ?>">
                                        <i class="bi <?= $acaoIcone ?>"></i> <?= $acaoTexto ?>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmarExclusao"
                                        data-id="<?= $grupo['id'] ?>"
                                        data-nome="<?= htmlspecialchars($grupo['nome']) ?>">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhum grupo encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <?php if ($total_paginas > 1): ?>
        <nav aria-label="Navegação de páginas">
            <ul class="pagination">
                <li class="page-item <?= ($pagina_atual <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= Funcoes::urlPaginacao($pagina_atual - 1) ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for ($i = max(1, $pagina_atual - 2); $i <= min($total_paginas, $pagina_atual + 2); $i++): ?>
                    <li class="page-item <?= ($i == $pagina_atual) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= Funcoes::urlPaginacao($i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($pagina_atual >= $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= Funcoes::urlPaginacao($pagina_atual + 1) ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="confirmarExclusao" tabindex="-1" aria-labelledby="confirmarExclusaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarExclusaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir o grupo <strong id="nomeGrupo"></strong>?
                <p class="text-danger mt-2">Esta ação também excluirá todos os produtos associados a este grupo.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="btnExcluir" class="btn btn-danger">Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Função de pesquisa com validação
    var search = document.getElementById("pesquisar");

    // Verificar tecla pressionada e chamar a função searchData() se for Enter
    search.addEventListener("keyup", function(event) {
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        const termo = search.value.trim();
        // Manter o filtro de status atual na busca
        const status = '<?= $filtro_status ?>';
        window.location = 'grupos.php?search=' + encodeURIComponent(termo) + '&status=' + status;
    }

    // Configuração do modal de confirmação
    document.getElementById('confirmarExclusao').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nome = button.getAttribute('data-nome');

        document.getElementById('nomeGrupo').textContent = nome;
        document.getElementById('btnExcluir').href = 'excluir_grupo.php?id=' + id;
    });

    // Fechar alertas automaticamente após 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const alertList = document.querySelectorAll('.alert');
        alertList.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>




<?php $this->stop(); ?>