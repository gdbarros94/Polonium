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
        // Hashes fixos para teste (gerados previamente)
        $users = [
            'admin' => [
                // senha: admin123
                'password' => '$argon2id$v=19$m=65536,t=4,p=1$VlBaWVhocXRpcHBLSXdNZA$junmjqeOW2EN90RPy0Z5MLxu30YgUVg4/yrvY0pzqs4',
                'role' => 'admin'
            ],
            'user' => [
                // senha: user123
                'password' => '$argon2id$v=19$m=65536,t=4,p=1$cXNHUzU3aVBUbEUudEZLVQ$qLfEhKVj0ssf7re1zwiOsHWL4bA7Y+y1CqEJY9x5p0c',
                'role' => 'user'
            ]
        ];
        if (isset($users[$user]) && AuthHandler::verifyPassword($pass, $users[$user]['password'])) {
            AuthHandler::login($user, $users[$user]['role']);
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
        $html = '<nav class="h-full"><ul class="space-y-2">';
        foreach ($plugins as $slug => $plugin) {
            $name = htmlspecialchars($plugin['name'] ?? $slug);
            $desc = htmlspecialchars($plugin['description'] ?? '');
            $routes = $plugin['routes'] ?? [];
            $mainRoute = is_array($routes) && count($routes) ? $routes[0] : '#';
            $html .= "<li><a href=\"{$mainRoute}\" class=\"block px-4 py-2 rounded hover:bg-indigo-100 text-indigo-700 font-semibold\" title=\"{$desc}\">{$name}</a></li>";
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