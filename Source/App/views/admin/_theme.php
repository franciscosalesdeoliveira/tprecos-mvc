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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>


</head>

<body>

    <nav class="main_nav">
        <?php if ($this->section("sidebar")):
            echo $this->section("sidebar");

        else:
        ?>
            <a title="" href="<?= url("admin/index"); ?>">Home</a>
            <a title="" href="<?= url("contato") ?>">Contato</a>
            <a title="" href="<?= url("teste") ?>">Teste</a>

            <?= $this->section("menu"); ?>
        <?php
        endif; ?>
    </nav>

    <main class="main_content">
        <?= $this->section("conteudo"); ?>
    </main>

    <footer class="footer bg-dark py-3 footer-custom fixed-bottom">
        <div class="container" style="min-width: 100%; max-width: 100%; height: 61px;">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-12 text-center ">
                    <p class="mt-2 mb-1 small text-secondary footer-custom-text">&copy; <?php echo date('Y'); ?> TadsBr Softwares. Todos os direitos reservados.</p>
                    <p class="mb-0 small">
                        <i class="fab fa-whatsapp" style="color: green"></i>
                        <a href="https://wa.me/5515981813900" target="_blank" class="text-light text-decoration-none">(15) 98181-3900</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Carregamento automático do Font Awesome -->
    <script>
        if (document.querySelectorAll('link[href*="font-awesome"]').length === 0) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
            document.head.appendChild(link);
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <?= $this->section("scripts"); ?>

</body>

</html>