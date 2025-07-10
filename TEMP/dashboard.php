<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="<?= url("public/assets/css/dashboard.css") ?>">

</head>

<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                🏢 Sistema Administrativo
            </div>
            <div class="user-info">
                <span>👤 <?= ($_SESSION['user_name']) ?></span>
                <span>|</span>
                <span><?= ($_SESSION['empresa_nome']) ?></span>
                <span><?= date('d/m/Y H:i') ?></span>
                <a class="btn btn-primary" href="<?= url("admin/logout") ?>">Sair</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-section">
            <h1 class="welcome-title">Bem-vindo ao Painel Administrativo</h1>
            <p class="welcome-subtitle">Gerencie empresas, usuários e configurações do sistema</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Empresas Ativas</span>
                    <div class="stat-icon" style="background: var(--success-color);">🏢</div>
                </div>
                <div class="stat-number"><?= number_format($empresas_ativas) ?></div>
                <div class="stat-label">Cadastradas e ativas</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Empresas Inativas</span>
                    <div class="stat-icon" style="background: var(--warning-color);">⏸️</div>
                </div>
                <div class="stat-number"><?= number_format($empresas_inativas) ?></div>
                <div class="stat-label">Temporariamente inativas</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total de Empresas</span>
                    <div class="stat-icon" style="background: var(--primary-color);">📊</div>
                </div>
                <div class="stat-number"><?= number_format($total_empresas) ?></div>
                <div class="stat-label">Cadastros no sistema</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Usuários Ativos</span>
                    <div class="stat-icon" style="background: var(--info-color);">👥</div>
                </div>
                <div class="stat-number"><?= number_format($usuarios_ativos) ?></div>
                <div class="stat-label">Usuários do sistema</div>
            </div>
        </div>

        <div class="main-content">
            <div class="modules-section">
                <h2 class="section-title">🚀 Módulos do Sistema</h2>
                <div class="modules-grid">
                    <a href="cadastro_empresa.php" class="module-card empresas">
                        <div class="module-icon">🏢</div>
                        <div class="module-title">Cadastro de Empresas</div>
                        <div class="module-description">
                            Cadastre e gerencie empresas utilizando consulta automática de CNPJ
                        </div>
                    </a>

                    <a href="cadastro_usuarios.php" class="module-card usuarios">
                        <div class="module-icon">👤</div>
                        <div class="module-title">Cadastro de Usuários</div>
                        <div class="module-description">
                            Gerencie usuários do sistema e suas permissões de acesso
                        </div>
                    </a>

                    <a href="listar_empresas.php" class="module-card empresas">
                        <div class="module-icon">📋</div>
                        <div class="module-title">Listar Empresas</div>
                        <div class="module-description">
                            Visualize, edite e gerencie todas as empresas cadastradas
                        </div>
                    </a>

                    <a href="relatorios.php" class="module-card relatorios">
                        <div class="module-icon">📈</div>
                        <div class="module-title">Relatórios</div>
                        <div class="module-description">
                            Gere relatórios detalhados e análises do sistema
                        </div>
                    </a>

                    <a href="gerenciar_usuarios.php" class="module-card usuarios">
                        <div class="module-icon">
                            <img src="./imgs/perfil.png" alt="">
                        </div>
                        <div class="module-title">Gerenciar Usuários</div>
                        <div class="module-description">
                            Configure permissões, roles e gerencie usuários do sistema
                        </div>
                    </a>

                    <a href="backup.php" class="module-card configuracoes">
                        <div class="module-icon">💾</div>
                        <div class="module-title">Backup</div>
                        <div class="module-description">
                            Realize backup e restauração dos dados do sistema
                        </div>
                    </a>
                </div>
            </div>

            <div class="recent-activity">
                <h2 class="section-title">📅 Atividade Recente</h2>
                <?php if (!empty($ultimas_empresas)): ?>
                    <ul class="activity-list">
                        <?php foreach ($ultimas_empresas as $empresa): ?>
                            <li class="activity-item">
                                <div class="activity-company"><?= htmlspecialchars($empresa['razao_social']) ?></div>
                                <?php if ($empresa['fantasia']): ?>
                                    <div class="activity-fantasy"><?= htmlspecialchars($empresa['fantasia']) ?></div>
                                <?php endif; ?>
                                <div class="activity-date">
                                    Cadastrada em <?= $empresa['criado_em'] ? date('d/m/Y H:i', strtotime($empresa['criado_em'])) : 'Data não disponível' ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Nenhuma empresa cadastrada recentemente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Animações e interatividade
        document.addEventListener('DOMContentLoaded', function() {
            // Animação de entrada para os cards
            const cards = document.querySelectorAll('.stat-card, .module-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Atualização automática do horário
            function atualizarHorario() {
                const agora = new Date();
                const formatter = new Intl.DateTimeFormat('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    // hour12: true,
                    timeZone: 'America/Sao_Paulo'
                });

                const horarioElement = document.querySelector('.user-info span:last-child');
                if (horarioElement) {
                    horarioElement.textContent = formatter.format(agora);
                }
            }

            // ✅ Chama a função imediatamente ao carregar a página
            atualizarHorario();

            // ✅ E depois a cada segundo
            // setInterval(atualizarHorario, 60000); // Atualiza a cada minuto
            setInterval(atualizarHorario, 1000);

            // Efeito de hover nos módulos
            const moduleCards = document.querySelectorAll('.module-card');
            moduleCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>

</html>