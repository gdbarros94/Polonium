<?php

// Rota principal do painel admin, protegida por autenticação e permissão
RoutesHandler::addRoute("GET", "/admin", function() {
    // Renderize um template ou inclua um arquivo
    // Exemplo usando ThemeHandler (ajuste conforme seu tema)
    ThemeHandler::render_header(['title' => 'Painel de Administração']);
    echo "<h1>Página do Painel de Admin</h1>";
    echo "<p>Bem-vindo ao painel administrativo!</p>";
    ThemeHandler::render_footer();
}, [
    "auth" => true,
    "permission" => "admin"
]);

// Rota de login (pública)
RoutesHandler::addRoute("GET", "/login", function() {
    ThemeHandler::render_header(['title' => 'Login']);
    echo "<h1>Página de Login</h1>";
    // Exemplo: listar rotas registradas
    $routes = RoutesHandler::getRoutes();
    echo "<ul>";
    foreach ($routes as $route) {
        echo "<li>" . htmlspecialchars($route['pattern']) . "</li>";
    }
    echo "</ul>";
    ThemeHandler::render_footer();
});

// Rota de logout (protegida)
RoutesHandler::addRoute("GET", "/admin/logout", function() {
    AuthHandler::logout();
    // O método logout já faz redirect, mas por segurança:
    header("Location: /login");
    exit;
}, [
    "auth" => true
]);

// Exemplo de hook: log após gerar relatório
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin admin-panel executado após gerar relatório.", "info");
});

// Log de carregamento do plugin
System::log("Plugin admin-panel carregado com sucesso.");