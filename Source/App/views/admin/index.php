<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Preços - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url("public/assets/css/index.css") ?>">
</head>

<body>
    <div class="dashboard">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Dashboard de Preços</h1>
            <p class="dashboard-subtitle">Gerencie sua tabela de preços de forma simples e eficiente</p>
            <div class="dashboard-wave">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                </svg>
            </div>
            <div class="dashboard-user-info">
                <p>Bem-vindo, <span style="color:rgb(3, 52, 109); font-weight: bold;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span></p>
                <hr>
            </div>
        </div>

        <div>
            <a class="btn btn-primary btn-sm position-absolute bottom-30 end-0 m-3" href="<?= url("admin/logout") ?>">
                <i class="fa-solid fa-right-from-bracket me-2 btn btn-primary"></i>Sair
            </a>
        </div>

        <div class="dashboard-content">
            <div class="menu-grid">
                <div class="stat-card">
                    <a href="<?= url("admin/tabela-precos") ?>" target="_blank" class="menu-card">
                        <div class="menu-icon">
                            <i class="fas fa-table"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Tabela de Preços</h3>
                            <p class="menu-description">Visualize e gerencie todos os preços cadastrados no sistema</p>
                        </div>
                    </a>
                </div>

                <div class="stat-card">
                    <a href="<?= url("admin/grupos") ?>" class="menu-card">
                        <div class="menu-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Cadastro de Grupos</h3>
                            <p class="menu-description">Crie e organize grupos para classificar seus produtos</p>
                        </div>
                    </a>
                </div>

                <div class="stat-card">
                    <a href="<?= url("admin/produtos") ?>" class="menu-card">
                        <div class="menu-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Cadastro de Produtos</h3>
                            <p class="menu-description">Adicione novos produtos e gerencie os existentes</p>
                        </div>
                    </a>
                </div>

                <div class="stat-card">
                    <a href="<?= url("admin/excel") ?>" class="menu-card">
                        <div class="menu-icon">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Importar CSV</h3>
                            <p class="menu-description">Importe produtos em massa a partir de arquivos CSV</p>
                        </div>
                    </a>
                </div>

                <div class="stat-card">
                    <a href="<?= url("admin/configuracoes") ?>" class="menu-card">
                        <div class="menu-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Configurações</h3>
                            <p class="menu-description">Personalize as configurações da sua tabela de preços</p>
                        </div>
                    </a>
                </div>

                <div class="stat-card">
                    <a href="<?= url("admin/contato") ?>" class="menu-card">
                        <div class="menu-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Fale Conosco</h3>
                            <p class="menu-description">Entre em contato para suporte e atendimento</p>
                        </div>
                    </a>
                </div>

                <div class="stat-card">
                    <a href="<?= url("admin/propagandas") ?>" class="menu-card">
                        <div class="menu-icon">
                            <i class="fa-solid fa-rectangle-ad"></i>
                        </div>
                        <div class="menu-content">
                            <h3 class="menu-title">Gerenciar Propagandas</h3>
                            <p class="menu-description">Gerencie as propagandas do sistema</p>
                        </div>
                    </a>
                </div>

            </div>
        </div>
</body>

</html>