<?php $this->layout("_theme", ["title" => $title]); ?>
<?php $this->start("conteudo"); ?>

<a class="btn btn-primary m-3" href="<?= url("admin/grupos/create"); ?>">
  <i class="fas fa-plus"></i> Adicionar
</a>
<div class="container py-4">
  <div class="table-responsive">
    <table class="table table-hover table-striped table-outline-horizontal mb-0">
      <thead class="sticky-header">
        <tr>
          <th scope="col" class="text-left">ID</th>
          <th scope="col" class="text-center">Nome</th>
          <th scope="col" class="text-center">Status</th>
          <th scope="col" class="text-center">A&ccedil;&otilde;es</th>
        </tr>
      </thead>
      <tbody>
        <?php if (($dataDB == null) or (count($dataDB) == 0)) :  ?>
          <tr class="">
            <td class="text-center" colspan="7">Sem dados cadastrados</td>
          </tr>
          <?php else :
          foreach ($dataDB as $key => $value) : ?>
            <tr>
              <td class="text-left"><?= $value->id ?></td>
              <td class="text-center"><?= $value->nome ?></td>
              <td class="text-center">
                <span class="badge <?= ($value->ativo == 'S')  ? 'bg-success' : 'bg-danger' ?>">
                  <?= ($value->ativo == 'S') ? 'Ativo' : 'Inativo' ?>
                </span>
              </td>
              <td class="text-center">
                <a class="btn btn-primary" href="<?= url("admin/grupos/{$value->id}"); ?>">
                  <i class="fas fa-edit"></i>
                </a>
                <form id="del<?= $value->id ?>"
                  action="<?= url("admin/grupos/{$value->id}/delete"); ?>"
                  method="POST"
                  style="display:inline;">
                  <button type="button" class="btn btn-danger"
                    onclick="if(confirm('Deseja excluir o grupo <?= $value->nome ?> e todos os seus produtos?')) { document.getElementById('del<?= $value->id ?>').submit(); }">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->stop(); ?>