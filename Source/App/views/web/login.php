<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Preços</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url("public/assets/css/login.css") ?>">

</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-chart-line"></i>
            <h1>Sistema de Preços</h1>
            <p>Acesse sua conta para continuar</p>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Email
                </label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username"
                        placeholder="Digite seu email"
                        value="<?php echo htmlspecialchars($username ?? ''); ?>"
                        required autocomplete="username">
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Senha
                </label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password"
                        placeholder="Digite sua senha"
                        required autocomplete="current-password">
                    <i class="fas fa-eye input-icon" id="togglePassword" style="cursor: pointer;"></i>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Verificando credenciais...</p>
            </div>
        </form>

        <div class="forgot-password">
            <a href="#" onclick="alert('Entre em contato com o administrador do sistema.')">
                <i class="fas fa-question-circle"></i> Esqueceu sua senha?
            </a>
        </div>

        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Sistema de Preços. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this;

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form submission with loading
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const loading = document.getElementById('loading');

            loginBtn.style.display = 'none';
            loading.style.display = 'block';

            // Se houver erro, restaurar o botão após 3 segundos
            setTimeout(function() {
                if (loading.style.display !== 'none') {
                    loginBtn.style.display = 'block';
                    loading.style.display = 'none';
                }
            }, 3000);
        });

        // Focus animations
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'scale(1)';
            });
        });

        // Auto-focus no primeiro campo
        document.getElementById('username').focus();

        // Enter key navigation
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });
    </script>
</body>

</html>