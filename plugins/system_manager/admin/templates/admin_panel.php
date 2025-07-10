<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - CoreCRM</title>
</head>
<body>
    <?php
    // Renderiza o header do tema
    ThemeHandler::render_header(['title' => 'Painel Admin - CoreCRM']);
    ?>
    <div class="min-h-screen flex bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white/90 shadow-lg p-6 flex flex-col min-h-screen">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-indigo-700 mb-2">Painel Admin</h2>
                <p class="text-gray-500 text-sm">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'Usuário'); ?>!</p>
                <p class="text-gray-400 text-xs mb-2">Papel: <?php echo htmlspecialchars($_SESSION['user_role'] ?? 'N/A'); ?></p>
            </div>
            <?php echo $sidebarMenu; ?>
            <div class="mt-auto pt-8">
                <a href="/logout" class="block text-center text-red-600 hover:underline font-semibold">Sair</a>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 p-10 flex flex-col gap-6">
            <h1 class="text-2xl font-bold text-indigo-800 mb-4">Dashboard</h1>
            <div class="space-y-6">
                <?php
                // Renderiza conteúdo registrado por plugins
                if (!empty($adminContentCallbacks)) {
                    foreach ($adminContentCallbacks as $callback) {
                        if (is_callable($callback)) {
                            call_user_func($callback);
                        }
                    }
                } else {
                    echo '<div class="text-gray-400 text-center">Nenhum plugin adicionou conteúdo ao painel ainda.</div>';
                }
                ?>
            </div>
        </main>
    </div>
    <?php ThemeHandler::render_footer(); ?>
</body>
</html>
