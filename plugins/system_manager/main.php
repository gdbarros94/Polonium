<?php
// Carrega os módulos do plugin system_manager
require_once __DIR__ . '/admin/admin.php';
require_once __DIR__ . '/api/token.php';
require_once __DIR__ . '/api/users.php';
require_once __DIR__ . '/admin/users_crud.php';

// As classes SystemManagerAdmin, SystemManagerTokenApi e SystemManagerUsersApi
// já registram suas rotas ao serem carregadas, não é necessário chamar registerRoutes() aqui novamente.

// Exemplo de hook: log após gerar relatório
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin system-manager executado após gerar relatório.", "info");
});

// Log de carregamento do plugin
System::log("Plugin system_manager carregado com sucesso.");

// Executa a migration de plugins ao iniciar o plugin
try {
    $pdo = DatabaseHandler::getConnection();
    require_once __DIR__ . '/admin/migrations/plugins_migration.php';
    SystemManagerPluginsMigration::migrate($pdo);
} catch (Exception $e) {
    System::log('Erro ao executar migration de plugins: ' . $e->getMessage(), 'error');
}

// Executa a migration de usuários ao iniciar o plugin
try {
    $pdo = DatabaseHandler::getConnection();
    require_once __DIR__ . '/admin/migrations/users_migration.php';
    require_once __DIR__ . '/admin/migrations/plugins_migration.php';
    SystemManagerUsersMigration::migrate($pdo);
} catch (Exception $e) {
    System::log('Erro ao executar migration de usuários: ' . $e->getMessage(), 'error');
}