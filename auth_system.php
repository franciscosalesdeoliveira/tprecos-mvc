<?php

/**
 * Sistema de Autenticação e Permissões
 * Classe para gerenciar autenticação, autorização e permissões de usuários
 */

class AuthSystem
{
    private static $pdo;
    private static $instance = null;

    public function __construct($pdo)
    {
        self::$pdo = $pdo;
    }

    public static function getInstance($pdo = null)
    {
        if (self::$instance === null) {
            if ($pdo === null) {
                throw new Exception('PDO connection required for first instance');
            }
            self::$instance = new self($pdo);
        }
        return self::$instance;
    }

    /**
     * Verificar se o usuário está logado
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_uuid']);
    }

    /**
     * Verificar se o usuário é administrador
     */
    public static function isAdmin($userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId) return false;

        try {
            $sql = "SELECT perfil_codigo FROM vw_usuarios_permissoes WHERE usuario_id = ?";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$userId]);
            $perfil = $stmt->fetchColumn();
            return $perfil === 'ADMIN';
        } catch (PDOException $e) {
            error_log("Erro ao verificar admin: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar se o usuário tem uma permissão específica
     */
    public static function hasPermission($permission, $userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId) return false;

        try {
            $sql = "SELECT COUNT(*) FROM vw_usuarios_permissoes 
                    WHERE usuario_id = ? AND ? = ANY(permissoes)";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$userId, $permission]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar permissão: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter nível de acesso do usuário
     */
    public static function getUserLevel($userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId) return 1;

        try {
            $sql = "SELECT nivel_acesso FROM vw_usuarios_permissoes WHERE usuario_id = ?";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$userId]);
            $nivel = $stmt->fetchColumn();
            return $nivel ? (int)$nivel : 1;
        } catch (PDOException $e) {
            error_log("Erro ao obter nível do usuário: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Obter perfil do usuário
     */
    public static function getUserProfile($userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId) return null;

        try {
            $sql = "SELECT perfil_codigo, perfil_nome, nivel_acesso, permissoes 
                    FROM vw_usuarios_permissoes WHERE usuario_id = ?";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao obter perfil do usuário: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar se a sessão expirou
     */
    public static function isSessionExpired($timeLimit = 28800)
    { // 8 horas por padrão
        if (!isset($_SESSION['login_time'])) {
            return true;
        }
        return (time() - $_SESSION['login_time'] > $timeLimit);
    }

    /**
     * Renovar tempo da sessão
     */
    public static function renewSession()
    {
        $_SESSION['login_time'] = time();
    }

    /**
     * Fazer logout do usuário
     */
    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    /**
     * Redirecionar se não estiver logado
     */
    public static function requireLogin($redirectTo = 'login.php')
    {
        if (!self::isLoggedIn()) {
            header("Location: $redirectTo");
            exit;
        }

        if (self::isSessionExpired()) {
            self::logout();
            header("Location: $redirectTo?msg=sessao_expirada");
            exit;
        }

        self::renewSession();
    }

    /**
     * Redirecionar se não tiver permissão
     */
    public static function requirePermission($permission, $redirectTo = 'dashboard.php')
    {
        self::requireLogin();

        if (!self::hasPermission($permission)) {
            header("Location: $redirectTo?msg=sem_permissao");
            exit;
        }
    }

    /**
     * Redirecionar se não for admin
     */
    public static function requireAdmin($redirectTo = 'dashboard.php')
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            header("Location: $redirectTo?msg=acesso_negado");
            exit;
        }
    }

    /**
     * Verificar se pode gerenciar usuário (baseado no nível)
     */
    public static function canManageUser($targetUserId, $currentUserId = null)
    {
        if (!$currentUserId) {
            $currentUserId = $_SESSION['user_id'] ?? null;
        }

        if (!$currentUserId) return false;

        // Admin pode gerenciar qualquer usuário
        if (self::isAdmin($currentUserId)) {
            return true;
        }

        // Obter níveis dos usuários
        $currentLevel = self::getUserLevel($currentUserId);
        $targetLevel = self::getUserLevel($targetUserId);

        // Só pode gerenciar usuários de nível inferior
        return $currentLevel > $targetLevel;
    }

    /**
     * Obter lista de permissões do usuário
     */
    public static function getUserPermissions($userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? null;
        }

        if (!$userId) return [];

        try {
            $sql = "SELECT permissoes FROM vw_usuarios_permissoes WHERE usuario_id = ?";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetchColumn();

            if ($result) {
                // PostgreSQL retorna array como string, converter para array PHP
                $permissions = str_replace(['{', '}'], '', $result);
                return $permissions ? explode(',', $permissions) : [];
            }

            return [];
        } catch (PDOException $e) {
            error_log("Erro ao obter permissões: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar múltiplas permissões (AND)
     */
    public static function hasAllPermissions($permissions, $userId = null)
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermission($permission, $userId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verificar se tem pelo menos uma das permissões (OR)
     */
    public static function hasAnyPermission($permissions, $userId = null)
    {
        foreach ($permissions as $permission) {
            if (self::hasPermission($permission, $userId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Registrar tentativa de acesso não autorizado
     */
    public static function logUnauthorizedAccess($action, $userId = null)
    {
        if (!$userId) {
            $userId = $_SESSION['user_id'] ?? 'guest';
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        error_log("ACESSO NÃO AUTORIZADO - Usuário: $userId, Ação: $action, IP: $ip, User-Agent: $userAgent");

        // Aqui você pode implementar salvamento em banco de dados se necessário
        try {
            $sql = "INSERT INTO logs_seguranca (usuario_id, acao, ip, user_agent, criado_em) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$userId, "ACESSO_NEGADO: $action", $ip, $userAgent]);
        } catch (PDOException $e) {
            // Ignora erro se tabela não existe
        }
    }

    /**
     * Middleware para verificação de permissões em APIs
     */
    public static function apiMiddleware($requiredPermission = null, $requiredLevel = 1)
    {
        // Verificar se está logado
        if (!self::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }

        // Verificar se sessão não expirou
        if (self::isSessionExpired()) {
            http_response_code(401);
            echo json_encode(['error' => 'Sessão expirada']);
            exit;
        }

        // Verificar permissão específica
        if ($requiredPermission && !self::hasPermission($requiredPermission)) {
            self::logUnauthorizedAccess($requiredPermission);
            http_response_code(403);
            echo json_encode(['error' => 'Sem permissão']);
            exit;
        }

        // Verificar nível mínimo
        if (self::getUserLevel() < $requiredLevel) {
            self::logUnauthorizedAccess("NIVEL_$requiredLevel");
            http_response_code(403);
            echo json_encode(['error' => 'Nível de acesso insuficiente']);
            exit;
        }

        // Renovar sessão
        self::renewSession();
    }

    /**
     * Gerar menu baseado nas permissões do usuário
     */
    public static function generateMenu()
    {
        $menu = [];
        $userPermissions = self::getUserPermissions();

        // Dashboard - sempre disponível para usuários logados
        $menu[] = [
            'title' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'url' => 'dashboard.php',
            'active' => true
        ];

        // Módulo Usuários
        if (self::hasAnyPermission(['USER_READ', 'USER_CREATE', 'USER_UPDATE', 'USER_DELETE'])) {
            $submenu = [];

            if (self::hasPermission('USER_READ')) {
                $submenu[] = ['title' => 'Listar', 'url' => 'listar_usuarios.php'];
            }
            if (self::hasPermission('USER_CREATE')) {
                $submenu[] = ['title' => 'Cadastrar', 'url' => 'cadastro_usuarios.php'];
            }

            $menu[] = [
                'title' => 'Usuários',
                'icon' => 'fas fa-users',
                'submenu' => $submenu
            ];
        }

        // Módulo Empresas
        if (self::hasAnyPermission(['COMPANY_READ', 'COMPANY_CREATE', 'COMPANY_UPDATE', 'COMPANY_DELETE'])) {
            $submenu = [];

            if (self::hasPermission('COMPANY_READ')) {
                $submenu[] = ['title' => 'Listar', 'url' => 'listar_empresas.php'];
            }
            if (self::hasPermission('COMPANY_CREATE')) {
                $submenu[] = ['title' => 'Cadastrar', 'url' => 'cadastro_empresas.php'];
            }

            $menu[] = [
                'title' => 'Empresas',
                'icon' => 'fas fa-building',
                'submenu' => $submenu
            ];
        }

        // Módulo Relatórios
        if (self::hasPermission('REPORT_VIEW')) {
            $menu[] = [
                'title' => 'Relatórios',
                'icon' => 'fas fa-chart-bar',
                'url' => 'relatorios.php'
            ];
        }

        // Módulo Sistema (apenas Admin)
        if (self::isAdmin()) {
            $submenu = [
                ['title' => 'Configurações', 'url' => 'configuracoes.php'],
                ['title' => 'Logs', 'url' => 'logs.php'],
                ['title' => 'Backup', 'url' => 'backup.php']
            ];

            $menu[] = [
                'title' => 'Sistema',
                'icon' => 'fas fa-cogs',
                'submenu' => $submenu
            ];
        }

        return $menu;
    }
}
