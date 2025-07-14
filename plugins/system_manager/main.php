<?php
// Carrega os módulos do plugin system_manager
require_once __DIR__ . '/admin/admin.php';
require_once __DIR__ . '/admin/users_crud.php';
require_once __DIR__ . '/api/token.php';
require_once __DIR__ . '/api/users.php';


// Exemplo de hook: log após gerar relatório
// HookHandler::register_hook("after_gerar_relatorio", function() {
    // System::log("Hook do plugin system-manager executado após gerar relatório.", "info");
// });


// Executa todas as migrations necessárias ao iniciar o plugin
try {
    $pdo = DatabaseHandler::getConnection();

    // Migration da tabela de plugins
    require_once __DIR__ . '/migrations/plugins_migration.php';
    SystemManagerPluginsMigration::migrate($pdo);

    // Migration da tabela de usuários
    require_once __DIR__ . '/migrations/users_migration.php';
    SystemManagerUsersMigration::migrate($pdo);

    // Migration da tabela de tokens de API
    require_once __DIR__ . '/migrations/api_tokens_migration.php';
    SystemManagerApiTokensMigration::migrate($pdo);

} catch (Exception $e) {
    System::log('Erro ao executar migrations do system_manager: ' . $e->getMessage(), 'error');
}

// Log de carregamento do plugin
System::log("Plugin system_manager carregado com sucesso.");