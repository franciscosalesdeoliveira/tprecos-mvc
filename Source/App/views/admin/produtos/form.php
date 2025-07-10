<?php $this->layout("_theme"); ?>

<?php $this->start("conteudo"); ?>

<div class="container-fluid py-4">
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

    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-center"><?php echo isset($produto_id) ? 'Editar Produto' : 'Cadastrar Produto'; ?></h1>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="form-section">
                <form method="POST" action="<?php echo url('admin/produtos'); ?>" class="row">
                    <?php if (isset($produto_id)): ?>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($produto_id); ?>">
                    <?php endif; ?>

                    <div class="col-md-4 mb-3">
                        <label for="nome" class="form-label">Nome do Produto:</label>
                        <input type="text" class="form-control" name="nome" id="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="grupo_id" class="form-label">Grupo:</label>
                        <select class="form-select" name="grupo_id" id="grupo_id" required>
                            <option value="">Selecione um grupo</option>
                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?php echo $grupo['id']; ?>" <?php echo (($grupo_id ?? '') == $grupo['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($grupo['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="preco" class="form-label">Preço:</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" name="preco" id="preco" value="<?php echo htmlspecialchars($preco ?? ''); ?>" step="0.01" required>
                        </div>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <input type="text" class="form-control" name="descricao" id="descricao" value="<?php echo htmlspecialchars($descricao ?? ''); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status:</label>
                        <div class="d-flex align-items-center mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" <?php echo ($ativo ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ativo">
                                    Ativo
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <a href="<?php echo url('admin/produtos'); ?>" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="reset" class="btn btn-warning me-2">
                            <i class="fas fa-eraser me-1"></i> Limpar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> <?php echo isset($produto_id) ? 'Atualizar' : 'Cadastrar'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->stop(); ?>