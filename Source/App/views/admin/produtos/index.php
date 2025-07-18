<?php $this->layout("_theme", ["title" => $title]); ?>
<?php $this->start("conteudo"); ?>


<a class="btn btn-primary m-3" href="<?= url("admin/produtos/create"); ?>">
    <i class="fas fa-plus"></i> Adicionar
</a>
<div class="container py-4">
    <!-- Exibir mensagens de erro -->
    <?php if (!empty($erros)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($erros as $erro): ?>
                    <li><?php echo htmlspecialchars($erro); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Exibir mensagem de sucesso -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class=" row">
        <!-- Coluna da Tabela -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Produtos</h5>
                    <div class="d-flex">
                        <!-- Filtro de Status -->
                        <div class="btn-group me-2" role="group">
                            <input type="radio" class="btn-check" name="filtroStatus" id="todos" value="todos" autocomplete="off" checked onclick="filtrarPorStatus('todos')">
                            <label class="btn btn-outline-light btn-sm" for="todos">Todos</label>

                            <input type="radio" class="btn-check" name="filtroStatus" id="ativos" value="ativos" autocomplete="off" onclick="filtrarPorStatus('ativos')">
                            <label class="btn btn-outline-light btn-sm" for="ativos">Ativos</label>

                            <input type="radio" class="btn-check" name="filtroStatus" id="inativos" value="inativos" autocomplete="off" onclick="filtrarPorStatus('inativos')">
                            <label class="btn btn-outline-light btn-sm" for="inativos">Inativos</label>
                        </div>

                        <!-- Pesquisa -->
                        <div class="input-group">
                            <input class="form-control form-control-sm" type="search" id="pesquisar" placeholder="Pesquisar..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button onclick="searchData()" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabela -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="sticky-header">
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Nome</th>
                                <th scope="col" class="text-center">Descrição</th>
                                <th scope="col" class="text-center">Grupo</th>
                                <th scope="col" class="text-center">Preço</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabela-produtos-body">
                            <?php foreach ($produtos as $produto): ?>
                                <tr class="produto-row <?= isset($produto['ativo']) && $produto['ativo'] ? 'produto-ativo' : 'produto-inativo' ?>"
                                    data-status="<?= isset($produto['ativo']) && $produto['ativo'] ? 'ativo' : 'inativo' ?>">
                                    <td class="text-center"><?= htmlspecialchars($produto['id']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($produto['nome']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($produto['descricao']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($produto['grupo_nome']) ?></td>
                                    <td class="text-center">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php if (isset($produto['ativo'])): ?>
                                            <span class="badge <?= $produto['ativo'] ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $produto['ativo'] ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center d-flex justify-content-center">
                                        <div class="d-flex justify-content-center">
                                            <a title="Editar" class="btn btn-primary btn-action" href="<?= url("admin/produtos/{$produto['id']}"); ?>&acao=editar&id=<?= $produto['id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="del<?= $produto['id'] ?>"
                                                action="<?= url("admin/produtos/{$produto['id']}"); ?>&acao=excluir&id=<?= $produto['id'] ?>"
                                                method="POST"
                                                style="display:inline;">

                                                <!-- Botão de Excluir -->
                                                <button type="button" class="btn btn-danger btn-action"
                                                    onclick="if(confirm('Tem certeza que deseja excluir este produto?')) { document.getElementById('del<?= $produto['id'] ?>').submit(); }">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var search = document.getElementById("pesquisar");

    // Verifica a tecla apertada e chama a função searchData() se for Enter
    search.addEventListener("keyup", function(event) {
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        window.location = '<?= url("admin/produtos"); ?>?search=' + search.value;
    }

    function filtrarPorStatus(status) {
        const rows = document.querySelectorAll('.produto-row');

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');

            if (status === 'todos') {
                row.style.display = '';
            } else if (status === 'ativos' && rowStatus === 'S') {
                row.style.display = '';
            } else if (status === 'inativos' && rowStatus === 'N') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
<?php $this->stop(); ?>