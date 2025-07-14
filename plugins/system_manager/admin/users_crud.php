<?php
class SystemManagerUsersCrud {
    public static function registerRoutes() {
        RoutesHandler::addRoute("GET", "/usuarios", [self::class, 'listUsers'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("GET", "/usuarios/novo", [self::class, 'newUserForm'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("POST", "/usuarios/novo", [self::class, 'createUser'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("GET", "/usuarios/editar/(\d+)", [self::class, 'editUserForm'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("POST", "/usuarios/editar/(\d+)", [self::class, 'updateUser'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("POST", "/usuarios/apagar/(\d+)", [self::class, 'deleteUser'], ["auth" => true, "permission" => "admin"]);
    }

    public static function listUsers() {
        $pdo = DatabaseHandler::getConnection();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/templates/users_list.php';
    }

    public static function newUserForm() {
        include __DIR__ . '/templates/user_form.php';
    }

    public static function createUser() {
        $pdo = DatabaseHandler::getConnection();
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
        $role = $_POST['role'] ?? 'user';
        $username = $_POST['username'] ?? '';
        $active = isset($_POST['active']) ? 1 : 0;
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, username, active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role, $username, $active]);
        header('Location: /usuarios');
        exit;
    }

    public static function editUserForm($id) {
        $pdo = DatabaseHandler::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        include __DIR__ . '/templates/user_form.php';
    }

    public static function updateUser($id) {
        $pdo = DatabaseHandler::getConnection();
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $username = $_POST['username'] ?? '';
        $active = isset($_POST['active']) ? 1 : 0;
        $password = $_POST['password'] ?? '';
        if ($password) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, password=?, role=?, username=?, active=? WHERE id=?");
            $stmt->execute([$name, $email, $password, $role, $username, $active, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, role=?, username=?, active=? WHERE id=?");
            $stmt->execute([$name, $email, $role, $username, $active, $id]);
        }
        header('Location: /usuarios');
        exit;
    }

    public static function deleteUser($id) {
        $pdo = DatabaseHandler::getConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: /usuarios');
        exit;
    }
}

// Registra item de menu lateral para Usuários
System::addAdminSidebarMenuItem([
    'name' => 'Usuários',
    'icon' => 'group',
    'url'  => '/admin/usuarios'
]);

SystemManagerUsersCrud::registerRoutes();
