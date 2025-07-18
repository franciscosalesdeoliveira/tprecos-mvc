<?php $this->layout("_theme", ["title" => $title]); ?>
<?php $this->start("conteudo"); ?>

<div class="container-fluid py-4">
  <form class="" action="<?= url("admin/grupos"); ?>" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="id" id="id" value="<?= isset($dataDB) ? $dataDB->id : ""; ?>" maxlength="20" class="form-control">

    <div class="form-group row">
      <div class="col-12 col-md-12">
        <label>Nome</label>
        <input type="text" name="nome" id="nome" value="<?= isset($dataDB) ? $dataDB->nome : ""; ?>" maxlength="75" class="form-control">
      </div>
    </div>

    <div class="col-6 col-md-6">
      <label>Ativo</label>
      <select class="form-control select-home" id="ativo" name="ativo">
        <option value="N" <?= isset($dataDB) && $dataDB->ativo === 'N' ? 'selected' : ''; ?>>Não</option>
        <option value="S" <?= isset($dataDB) && $dataDB->ativo === 'S' ? 'selected' : ''; ?>>Sim</option>
      </select>
    </div>
    <button type="submit" class="btn btn-success mt-4"><i class="fas fa-save"></i> Gravar</button>
  </form>
</div>

<?php $this->stop(); ?>