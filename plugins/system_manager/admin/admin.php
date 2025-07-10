<?php

class SystemManagerAdmin {
    private static $adminContentCallbacks = [];

    public static function registerRoutes() {
        // Rota de login (GET)
        RoutesHandler::addRoute("GET", "/login", [self::class, 'loginGet']);

        // Rota de login (POST)
        RoutesHandler::addRoute("POST", "/login", [self::class, 'loginPost']);

        // Rota principal do painel admin (protegida)
        RoutesHandler::addRoute("GET", "/admin", [self::class, 'adminPanel'], [
            "auth" => true,
            "permission" => "admin"
        ]);
        // Rota para CRUD de usuários dentro do admin
        RoutesHandler::addRoute("GET", "/admin/usuarios", function() {
            require_once __DIR__ . '/users_crud.php';
            SystemManagerUsersCrud::listUsers();
        }, ["auth" => true, "permission" => "admin"]);

        // Rota de logout
        RoutesHandler::addRoute("GET", "/logout", [self::class, 'logout']);
    }

    public static function loginGet() {
        $error = '';
        $redirect = $_GET['redirect'] ?? '/admin';
        include __DIR__ . '/templates/login.php';
    }

    public static function loginPost() {
        $error = '';
        $user = $_POST['user'] ?? '';
        $pass = $_POST['password'] ?? '';
        $redirect = $_POST['redirect'] ?? '/admin';
        // Busca usuário no banco de dados
        $pdo = DatabaseHandler::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$user, $user]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dbUser && AuthHandler::verifyPassword($pass, $dbUser['senha'])) {
            AuthHandler::login($dbUser['username'], $dbUser['tipo']);
            // Redireciona para a rota original, se for segura
            if (strpos($redirect, '/') === 0) {
                AuthHandler::redirect($redirect);
            } else {
                AuthHandler::redirect('/admin');
            }
        } else {
            $error = 'Usuário ou senha inválidos!';
            echo "<script>alert('$error');</script>";
            include __DIR__ . '/templates/login.php';
        }
    }

    public static function adminPanel() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        // Busca plugins ativos
        $plugins = PluginHandler::getActivePlugins();
        // Torna plugins disponíveis para o template
        $sidebarMenu = self::renderSidebarMenu($plugins);
        // Torna callbacks disponíveis para o template
        $adminContentCallbacks = \System::$adminContentCallbacks;
        include __DIR__ . '/templates/admin_panel.php';
    }

    /**
     * Permite que plugins registrem conteúdo para o painel admin
     * @param callable $callback
     */
    public static function addAdminContent(callable $callback) {
        self::$adminContentCallbacks[] = $callback;
    }

    /**
     * Renderiza o menu lateral com base nos plugins ativos
     * @param array $plugins
     * @return string HTML do menu
     */
    public static function renderSidebarMenu($plugins) {
        // Usa os itens registrados manualmente pelos plugins
        $items = \System::$adminSidebarMenuItems;
        $html = '<nav class="h-full"><ul class="space-y-2">';
        foreach ($items as $item) {
            $name = htmlspecialchars($item['name'] ?? 'Item');
            $icon = $item['icon'] ?? 'circle';
            $url = htmlspecialchars($item['url'] ?? '#');
            $html .= "<li><a href=\"{$url}\" class=\"flex items-center gap-2 px-4 py-2 rounded hover:bg-indigo-100 text-indigo-700 font-semibold\"><span class=\"material-icons text-base\">{$icon}</span>{$name}</a></li>";
        }
        $html .= '</ul></nav>';
        return $html;
    }

    public static function logout() {
        AuthHandler::logout();
        // O método logout já faz redirect para /login
    }
}

SystemManagerAdmin::registerRoutes();