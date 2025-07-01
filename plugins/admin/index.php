<?php

require_once __DIR__ . '/../bootstrap.php';

// Verificar autenticação e permissão de admin
AuthHandler::requireAuth();
if (!AuthHandler::checkPermission('admin')) {
    AuthHandler::redirect('/login'); // Ou uma página de acesso negado
}

// Conteúdo do painel de administração
ThemeHandler::render_header(['title' => 'Painel Administrativo']);

?>

<div class="container">
    <h1>Painel Administrativo</h1>
    <p>Bem-vindo ao painel de administração do CRM.</p>

    <h2>Plugins</h2>
    <ul>
        <?php foreach (PluginHandler::getActivePlugins() as $slug => $plugin): ?>
            <li><?php echo $plugin['name']; ?> (<?php echo $plugin['version']; ?>) - <?php echo $plugin['description']; ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Funcionalidade de upload e instalação de plugins a ser implementada.</p>

    <h2>Usuários e Permissões</h2>
    <p>Gerenciamento de usuários e permissões a ser implementado.</p>

    <h2>Logs do Sistema</h2>
    <pre><?php echo implode("\n", System::getLogs()); ?></pre>

    <h2>Configurações</h2>
    <p>Modo Debug: <?php global $config; echo $config['debug'] ? 'Ativo' : 'Inativo'; ?></p>
    <p>Tema Ativo: <?php echo ThemeHandler::getActiveTheme(); ?></p>
</div>

<?php
ThemeHandler::render_footer();
?>

