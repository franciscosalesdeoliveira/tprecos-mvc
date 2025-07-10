<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($title) && !empty($title)):
        $title = strip_tags($title);
    else:
        $title = "PHPTips";
    endif;

    ?>
    <title><?= $title ?></title>

    <link rel="stylesheet" href="<?= url("public/assets/css/style.css") ?>">


</head>

<body>

    <nav class="main_nav">
        <?php if ($this->section("sidebar")):
            echo $this->section("sidebar");

        else:
        ?>
            <a title="" href="<?= url(); ?>">Home</a>
            <a title="" href="<?= url("contato") ?>">Contato</a>
            <a title="" href="<?= url("teste") ?>">Teste</a>
            <a title="" href="<?= url("login") ?>">Login</a>
            <?= $this->section("menu"); ?>
        <?php
        endif; ?>
    </nav>

    <main class="main_content">
        <?= $this->section("conteudo"); ?>
    </main>

    <footer class="main_footer">
        <?= SITE ?> &copy; <?= date("Y") ?> - Todos os direitos reservados.
    </footer>


    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <?= $this->section("scripts"); ?>

</body>

</html>