<?php $this->layout("_theme"); ?>

<?php $this->start("conteudo"); ?>

<!-- Formulário de cadastro/edição -->
<div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white">
        <?= $propagandaEdicao ? 'Editar Propaganda' : 'Nova Propaganda' ?>
    </div>
    <div class="card-body">
        <form method="post" action="<?php echo url('admin/propagandas'); ?>" enctype="multipart/form-data">
            <?php if ($propagandaEdicao): ?>
                <input type="hidden" name="id" value="<?= $propagandaEdicao['id'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="titulo" class="form-label">Título da Propaganda</label>
                    <input type="text" class="form-control" id="titulo" name="titulo"
                        value="<?= $propagandaEdicao ? htmlspecialchars($propagandaEdicao['titulo']) : '' ?>">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="ordem" class="form-label">Ordem de Exibição</label>
                    <input type="number" class="form-control" id="ordem" name="ordem"
                        value="<?= $propagandaEdicao ? $propagandaEdicao['ordem'] : '0' ?>"
                        min="0">
                </div>

                <!-- Correção no formulário - adicionar value="S" ao checkbox -->
                <div class="col-md-3 mb-3">
                    <label class="form-label d-block">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="S"
                            <?php
                            // Correção na lógica de verificação
                            if ($propagandaEdicao) {
                                echo ($propagandaEdicao['ativo'] === 'S' || $propagandaEdicao['ativo'] === '1' || $propagandaEdicao['ativo'] === 1) ? 'checked' : '';
                            } else {
                                echo 'checked'; // Por padrão, nova propaganda deve ser ativa
                            }
                            ?>>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="2"><?= $propagandaEdicao ? htmlspecialchars($propagandaEdicao['descricao']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Imagem</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_imagem" id="tipo_local" value="local"
                        <?= (!$propagandaEdicao || ($propagandaEdicao && ($propagandaEdicao['tipo_imagem'] ?? 'local') == 'local')) ? 'checked' : '' ?>
                        onchange="toggleImagemFields()">
                    <label class="form-check-label" for="tipo_local">
                        Upload de Arquivo Local
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_imagem" id="tipo_url" value="url"
                        <?= ($propagandaEdicao && ($propagandaEdicao['tipo_imagem'] ?? 'local') == 'url') ? 'checked' : '' ?>
                        onchange="toggleImagemFields()">
                    <label class="form-check-label" for="tipo_url">
                        Link URL (Google Drive, etc.)
                    </label>
                </div>
            </div>

            <div id="campo_upload" class="mb-3">
                <label for="imagem" class="form-label">
                    <?= $propagandaEdicao ? 'Alterar Imagem (opcional)' : 'Imagem da Propaganda' ?>
                </label>
                <input type="file" class="form-control" name="imagem" id="imagem" accept="image/jpeg, image/png">

                <div class="form-text">Formatos suportados: JPG, PNG.</div>
            </div>

            <div id="campo_url" class="mb-3" style="display: none;">
                <label for="url_imagem" class="form-label">URL da Imagem</label>
                <input type="url" class="form-control" id="url_imagem" name="url_imagem"
                    placeholder="https://drive.google.com/file/d/SEU_ID_AQUI/view"
                    value="<?= ($propagandaEdicao && ($propagandaEdicao['tipo_imagem'] ?? 'local') == 'url') ? htmlspecialchars($propagandaEdicao['imagem']) : '' ?>">
                <div class="form-text">
                    <p>Para Google Drive:</p>
                    <ol class="small">
                        <li>Faça upload da imagem no Google Drive</li>
                        <li>Clique com botão direito → "Compartilhar" → "Qualquer pessoa com o link" → "Visualizador"</li>
                        <li>Abra a imagem e copie a URL do navegador</li>
                    </ol>
                    <p class="text-danger">⚠️ É essencial configurar o compartilhamento da imagem para "Qualquer pessoa com o link"</p>
                </div>
            </div>

            <?php if ($propagandaEdicao && $propagandaEdicao['imagem']): ?>
                <div class="mb-3">
                    <label class="form-label">Imagem Atual</label>
                    <div class="border p-2 rounded">
                        <img src="<?= htmlspecialchars($propagandaModel->getImagemUrl($propagandaEdicao)) ?>"
                            class="img-thumbnail" style="max-height: 150px;"
                            onerror="this.onerror=null;this.classList.add('border-danger');this.style.opacity='0.3';">
                        <?php if (($propagandaEdicao['tipo_imagem'] ?? 'local') == 'url'): ?>
                            <div class="mt-2 small">
                                <a href="<?= htmlspecialchars($propagandaEdicao['imagem']) ?>" target="_blank" class="text-info">
                                    <i class="bi bi-link-45deg"></i> Ver URL original
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    <?= $propagandaEdicao ? 'Atualizar Propaganda' : 'Salvar Nova Propaganda' ?>
                </button>
                <?php if ($propagandaEdicao): ?>
                    <a href="propagandas.php" class="btn btn-outline-secondary">Cancelar Edição</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php $this->stop(); ?>