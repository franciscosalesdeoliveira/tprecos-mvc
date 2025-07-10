<?php
$this->layout("_theme");
$this->start("conteudo");

?>


<h2>Usuario sem Permissao</h2>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque, odit?</p>
<a href="<?= url("login"); ?>" title="Voltar ao Inicio">Voltar</a>

<?php $this->stop(); ?>