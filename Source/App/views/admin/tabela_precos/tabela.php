<?php if (!$isAjax): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Produtos - <?= $config['grupoSelecionado'] === 'todos' ? 'Todos os Grupos' : 'Grupo: ' . htmlspecialchars($config['grupoSelecionado']) ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                <?php endif; ?>

                <div class="table-container" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Código</th>
                                <th>Produto</th>
                                <th>Grupo</th>
                                <th>Preço</th>
                                <th>Promoção</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($produtos)): ?>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= htmlspecialchars($produto['codigo'] ?? 'N/A') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($produto['nome'] ?? 'Produto sem nome') ?></strong>
                                            <?php if (!empty($produto['descricao'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($produto['descricao']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($produto['grupo'] ?? 'Sem grupo') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="h5 text-success">
                                                R$ <?= number_format($produto['preco'] ?? 0, 2, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] < $produto['preco']): ?>
                                                <span class="h6 text-danger">
                                                    <del>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></del>
                                                </span>
                                                <br>
                                                <span class="h5 text-warning">
                                                    R$ <?= number_format($produto['preco_promocional'], 2, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $produto['ativo'] ?? 1;
                                            $statusClass = $status ? 'bg-success' : 'bg-danger';
                                            $statusText = $status ? 'Ativo' : 'Inativo';
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>Nenhum produto encontrado</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!$isAjax): ?>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                Total de produtos: <?= count($produtos) ?>
                                <?php if ($config['limiteGrupo'] > 0): ?>
                                    (Limite: <?= $config['limiteGrupo'] ?> por grupo)
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Última atualização: <?= date('d/m/Y H:i:s') ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configurações de exibição -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Configurações Atuais</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small><strong>Tempo de Slide:</strong> <?= $config['tempoSlide'] / 1000 ?>s</small>
                        </div>
                        <div class="col-md-3">
                            <small><strong>Tempo de Rolagem:</strong> <?= $config['tempoRolagem'] ?>s</small>
                        </div>
                        <div class="col-md-3">
                            <small><strong>Atualização Auto:</strong> <?= $config['atualizacao_auto'] ?>min</small>
                        </div>
                        <div class="col-md-3">
                            <small><strong>Grupo:</strong> <?= htmlspecialchars($config['grupoSelecionado']) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>