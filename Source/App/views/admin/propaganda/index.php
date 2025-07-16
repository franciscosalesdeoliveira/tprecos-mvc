<?php
// Incluir a classe helper (ajustar o caminho conforme necessário)
require_once 'Source/helpers/ImageHelper.php';

$this->layout("_theme");
?>

<?php $this->start("conteudo"); ?>

<a href="<?= url("admin/propagandas/create"); ?>" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Adicionar
</a>
<div class="container mt-4">
    <h1 class="text-center mb-4" style="color: white;"><?= $titulo ?></h1>

    <?php if (isset($mensagem)): ?>
        <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show">
            <?= $mensagem ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Lista de propagandas -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            Propagandas Cadastradas
        </div>
        <div class="card-body">
            <?php if (empty($propagandas)): ?>
                <div class="alert alert-info">
                    Nenhuma propaganda cadastrada. Adicione sua primeira propaganda clicando no botão acima.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Ordem</th>
                                <th style="width: 100px;">Imagem</th>
                                <th>Título</th>
                                <th style="width: 100px;">Tipo</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($propagandas as $propaganda): ?>
                                <tr>
                                    <td class="text-center"><?= $propaganda['ordem'] ?></td>
                                    <td>
                                        <?php
                                        // Usar a classe helper para renderizar a imagem
                                        $debugMode = isset($_GET['debug']) && $_GET['debug'] == '1';
                                        echo ImageHelper::renderImage($propaganda, [
                                            'show_debug' => $debugMode
                                        ]);
                                        ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($propaganda['titulo']) ?></strong>
                                        <?php if (!empty($propaganda['descricao'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($propaganda['descricao']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= ($propaganda['tipo_imagem'] ?? 'local') == 'url' ? 'URL Externa' : 'Arquivo Local' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $ativo = ($propaganda['ativo'] === 'S' || $propaganda['ativo'] === '1' || $propaganda['ativo'] === 1 || $propaganda['ativo'] === true);
                                        ?>
                                        <?php if ($ativo): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?alterarstatus=<?= $propaganda['id'] ?>" class="btn btn-outline-<?= $ativo ? 'secondary' : 'success' ?>" title="<?= $ativo ? 'Desativar' : 'Ativar' ?>">
                                                <i class="bi bi-<?= $ativo ? 'toggle-on' : 'toggle-off' ?>"></i>
                                            </a>
                                            <a href="<?= url("admin/propagandas/{$propaganda['id']}/update") ?>" class="btn btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a href="?excluir=<?= $propaganda['id'] ?>" class="btn btn-outline-danger"
                                                onclick="return confirm('Tem certeza que deseja excluir esta propaganda?')" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botões de navegação -->
    <div class="mt-4 mb-5 text-center">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Voltar para Início
        </a>
        <a href="configuracoes.php" class="btn btn-outline-primary">
            <i class="bi bi-gear"></i> Configurações da Tabela
        </a>
        <?php if (isset($_GET['debug'])): ?>
            <a href="?" class="btn btn-outline-warning">
                <i class="bi bi-bug"></i> Desativar Debug
            </a>
        <?php else: ?>
            <a href="?debug=1" class="btn btn-outline-info">
                <i class="bi bi-bug"></i> Modo Debug
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleImagemFields() {
        const tipoLocal = document.getElementById('tipo_local');
        const campoUpload = document.getElementById('campo_upload');
        const campoUrl = document.getElementById('campo_url');

        if (tipoLocal && tipoLocal.checked) {
            if (campoUpload) campoUpload.style.display = 'block';
            if (campoUrl) campoUrl.style.display = 'none';
        } else {
            if (campoUpload) campoUpload.style.display = 'none';
            if (campoUrl) campoUrl.style.display = 'block';
        }
    }

    // Inicializar o estado dos campos
    document.addEventListener('DOMContentLoaded', function() {
        toggleImagemFields();
    });
</script>

<?php $this->stop(); ?>