<?php

// Rota de login (GET)
RoutesHandler::addRoute("GET", "/login", function() {
    $error = '';
    include __DIR__ . '/templates/login.php';
});

// Rota de login (POST)
RoutesHandler::addRoute("POST", "/login", function() {
    $error = '';
    $user = $_POST['user'] ?? '';
    $pass = $_POST['password'] ?? '';
    // Exemplo de "banco" de usuários (substitua por consulta real depois)
    $users = [
        'admin' => [
            'password' => AuthHandler::hashPassword('admin123'),
            'role' => 'admin'
        ],
        'user' => [
            'password' => AuthHandler::hashPassword('user123'),
            'role' => 'user'
        ]
    ];
    if (isset($users[$user]) && AuthHandler::verifyPassword($pass, $users[$user]['password'])) {
        AuthHandler::login($user, $users[$user]['role']);
        AuthHandler::redirect('/admin');
    } else {
        $error = 'Usuário ou senha inválidos!';
        include __DIR__ . '/templates/login.php';
    }
});

// Rota principal do painel admin (protegida)
RoutesHandler::addRoute("GET", "/admin", function() {
    AuthHandler::requireAuth();
    if (!AuthHandler::checkPermission('admin')) {
        echo "Acesso negado.";
        return;
    }
    include __DIR__ . '/templates/admin_panel.php';
}, [
    "auth" => true,
    "permission" => "admin"
]);

// Rota de logout
RoutesHandler::addRoute("GET", "/admin/logout", function() {
    AuthHandler::logout();
    // O método logout já faz redirect, não precisa de header extra
}, [
    "auth" => true
]);

// Exemplo de hook: log após gerar relatório
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin admin-panel executado após gerar relatório.", "info");
});

// Log de carregamento do plugin
System::log("Plugin admin-panel carregado com sucesso.");