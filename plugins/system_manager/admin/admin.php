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

        // Rota para página de plugins (GET e POST)
        RoutesHandler::addRoute("GET", "/admin/plugins", [self::class, 'pluginsPage'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("POST", "/admin/plugins", [self::class, 'pluginsUpload'], ["auth" => true, "permission" => "admin"]);
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

    public static function pluginsPage() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        $plugins = self::getAllPlugins();
        include '/templates/plugins.php';
    }

    public static function pluginsUpload() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        if (!isset($_FILES['plugin_zip']) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK) {
            echo "Erro no upload do arquivo.";
            return;
        }
        $zipPath = $_FILES['plugin_zip']['tmp_name'];
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            // Extrai para uma pasta temporária
            $tmpDir = sys_get_temp_dir() . '/plugin_' . uniqid();
            mkdir($tmpDir);
            $zip->extractTo($tmpDir);
            $zip->close();
            // Move a(s) pasta(s) extraída(s) para plugins/
            $pluginBase = dirname(__DIR__, 2) . '/plugins/';
            $moved = false;
            foreach (scandir($tmpDir) as $item) {
                if ($item === '.' || $item === '..') continue;
                $src = $tmpDir . '/' . $item;
                $dest = $pluginBase . $item;
                if (is_dir($src)) {
                    if (!file_exists($dest)) {
                        rename($src, $dest);
                        $moved = true;
                    }
                }
            }
            // Limpa temp
            array_map('unlink', glob("$tmpDir/*"));
            rmdir($tmpDir);
            if ($moved) {
                System::log("Plugin enviado e instalado com sucesso.", "info");
            } else {
                System::log("Nenhuma pasta de plugin foi movida. Talvez já exista.", "warning");
            }
        } else {
            echo "Não foi possível abrir o arquivo ZIP.";
            return;
        }
        header('Location: /admin/plugins');
        exit;
    }

    private static function getAllPlugins() {
        // Usa apenas PluginHandler::getActivePlugins() para listar plugins
        $activePlugins = PluginHandler::getActivePlugins();
        $plugins = [];
        foreach ($activePlugins as $slug => $meta) {
            $plugins[] = [
                'name' => $meta['name'] ?? $slug,
                'folder' => $slug,
                'active' => true
            ];
        }
        return $plugins;
    }
}

// Registra item de menu lateral para Plugins
\System::addAdminSidebarMenuItem([
    'name' => 'Plugins',
    'icon' => 'extension',
    'url'  => '/admin/plugins'
]);

SystemManagerAdmin::registerRoutes();