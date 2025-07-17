<?php $this->layout("_theme"); ?>
<?php $this->start("conteudo"); ?>

<?php
$isEdit = isset($dataDB);

?>

<div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white">
        <?= $isEdit ? 'Editar Propaganda' : 'Nova Propaganda' ?>
    </div>
    <div class="card-body">
        <form method="post" action="<?= url("admin/propagandas") ?>" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= isset($dataDB) ? $dataDB['id'] : '' ?>">


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="titulo" class="form-label">Título da Propaganda</label>
                    <input type="text" class="form-control" id="titulo" name="titulo"
                        value="<?= $isEdit ? htmlspecialchars($dataDB['titulo']) : '' ?>">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="ordem" class="form-label">Ordem de Exibição</label>
                    <input type="number" class="form-control" id="ordem" name="ordem"
                        value="<?= $isEdit ? $dataDB['ordem'] : '0' ?>" min="0">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label d-block">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="S"
                            <?= $isEdit
                                ? (($dataDB['ativo'] === 'S' || $dataDB['ativo'] == 1) ? 'checked' : '')
                                : 'checked' ?>>
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="2"><?= $isEdit ? htmlspecialchars($dataDB['descricao']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Imagem</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_imagem" id="tipo_local" value="local"
                        <?= !$isEdit || ($dataDB['tipo_imagem'] ?? 'local') === 'local' ? 'checked' : '' ?>
                        onchange="toggleImagemFields()">
                    <label class="form-check-label" for="tipo_local">
                        Upload de Arquivo Local
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_imagem" id="tipo_url" value="url"
                        <?= $isEdit && ($dataDB['tipo_imagem'] ?? 'local') === 'url' ? 'checked' : '' ?>
                        onchange="toggleImagemFields()">
                    <label class="form-check-label" for="tipo_url">
                        Link URL (Google Drive, etc.)
                    </label>
                </div>
            </div>

            <div id="campo_upload" class="mb-3">
                <label for="imagem" class="form-label">
                    <?= $isEdit ? 'Alterar Imagem (opcional)' : 'Imagem da Propaganda' ?>
                </label>
                <input type="file" class="form-control" name="imagem" id="imagem" accept="image/jpeg, image/png">
                <div class="form-text">Formatos suportados: JPG, PNG.</div>
            </div>

            <div id="campo_url" class="mb-3" style="display: none;">
                <label for="url_imagem" class="form-label">URL da Imagem</label>
                <input type="url" class="form-control" id="url_imagem" name="url_imagem"
                    placeholder="https://drive.google.com/file/d/SEU_ID_AQUI/view"
                    value="<?= $isEdit && ($dataDB['tipo_imagem'] ?? '') === 'url'
                                ? htmlspecialchars($dataDB['imagem'])
                                : '' ?>">
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


            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    <?= $isEdit ? 'Atualizar Propaganda' : 'Salvar Nova Propaganda' ?>
                </button>
                <?php if ($isEdit): ?>
                    <a href="<?= url('admin/propagandas') ?>" class="btn btn-outline-secondary">Cancelar Edição</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleImagemFields() {
        const tipoLocal = document.getElementById('tipo_local').checked;
        document.getElementById('campo_upload').style.display = tipoLocal ? 'block' : 'none';
        document.getElementById('campo_url').style.display = tipoLocal ? 'none' : 'block';
    }

    // Executa ao carregar
    window.addEventListener('DOMContentLoaded', toggleImagemFields);
</script>

<?php $this->stop(); ?>