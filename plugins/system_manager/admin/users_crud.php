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
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = password_hash($_POST['senha'] ?? '', PASSWORD_DEFAULT);
        $tipo = $_POST['tipo'] ?? 'usuario';
        $username = $_POST['username'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, tipo, username, ativo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $tipo, $username, $ativo]);
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
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $tipo = $_POST['tipo'] ?? 'usuario';
        $username = $_POST['username'] ?? '';
        $ativo = isset($_POST['ativo']) ? 1 : 0;
        $senha = $_POST['senha'] ?? '';
        if ($senha) {
            $senha = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET nome=?, email=?, senha=?, tipo=?, username=?, ativo=? WHERE id=?");
            $stmt->execute([$nome, $email, $senha, $tipo, $username, $ativo, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET nome=?, email=?, tipo=?, username=?, ativo=? WHERE id=?");
            $stmt->execute([$nome, $email, $tipo, $username, $ativo, $id]);
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
