<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Erro'; ?></title>
    <link rel="stylesheet" href="<?= url("public/assets/css/style.css") ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <nav class="navbar main_nav navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand  " href="<?= url("admin/") ?>">Home</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <?php if ($this->section("sidebar")):
                echo $this->section("sidebar");
            else:
            ?>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav gap-2">
                        <li class="nav-item">
                            <a class="nav-link" title="" href="<?= url("admin/contato") ?>">Contato</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" title="" href="<?= url("teste") ?>">Teste</a>
                        </li>
                        <!-- <li class="nav-item">

                            <a class="nav-link" title="" href="<?= url("https://tadsbr.com.br/") ?>"><img src="<?= url("public/assets/img/logo.png") ?>" alt="" style="width: 50px; height: 50px;"></a>
                        </li> -->
                    </ul>
                </div>
                <?= $this->section("menu"); ?>
            <?php
            endif; ?>
        </div>
    </nav>

    <main class="main_content">
        <?php if (!empty($_SESSION['messageSuccess'])): ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert" id="alert-message">
                <?= $_SESSION['messageSuccess'];
                unset($_SESSION['messageSuccess']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['messageError'])): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" id="alert-message">
                <?= $_SESSION['messageError'];
                unset($_SESSION['messageError']); ?>
            </div>
        <?php endif; ?>
        <?= $this->section("conteudo"); ?>
    </main>

    <footer class="bg-dark py-1 " style="height: 100px; overflow-x: hidden; ">
        <div class="" style="min-width: 100%; max-width: 100%; ">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-12 text-center ">
                    <p class="small text-secondary footer-custom-text">&copy; <?php echo date('Y'); ?> TadsBr Softwares. Todos os direitos reservados.</p>
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
    <script>
        setTimeout(function() {
            const alert = document.getElementById('alert-message');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // remove do DOM
            }
        }, 500); // 500ms
    </script>

</body>

</html>