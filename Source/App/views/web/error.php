<?php $this->layout("_theme"); ?>

<div class="error">
    <h2>Ocorreu um erro <?= $error; ?>!</h2>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
</div>

<?php $this->start("sidebar"); ?>
<a href="<?= url("admin/index"); ?>" title="Voltar ao Inicio">Voltar</a>
<?php $this->end(); ?>