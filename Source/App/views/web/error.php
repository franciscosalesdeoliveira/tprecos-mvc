<?php $this->layout("_theme"); ?>

<div class="error">
    <h2>Ocorreu um erro <?= $error; ?>!</h2>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
</div>



<?php $this->start("sidebar"); ?>
<div class="error">


</div>


<?php $this->stop(); ?>

<?= $this->start("conteudo"); ?>
<div class="error text-center" style="align-items: center; justify-content: center; margin-top: 15%;">
    <h2>Ocorreu um erro <?= $error; ?>!</h2>
    <?php
    if (isset($error) && $error == 404):
        echo "<p>Página não encontrada!</p>";
    elseif (isset($error) && $error == 500):
        echo "<p>Erro interno do servidor!</p>";
    elseif (isset($error) && $error == 403):
        echo "<p>Acesso negado!</p>";
    elseif (isset($error) && $error == 401):
        echo "<p>Acesso negado!</p>";
    else:
        echo "<p>Algo de errado aconteceu!</p>";
    endif;
    ?>
    <a style="text-decoration: none;" class="btn btn-primary" href="<?= url("admin/"); ?>" title="Voltar ao Inicio">Voltar a pagina inicial</a>
</div>

<?php $this->stop(); ?>