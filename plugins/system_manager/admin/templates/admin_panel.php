
<?php
require_once __DIR__ . '/../../../../themes/default/blocks/BlockRenderer.php';

// Sidebar data
$sidebar = [
    'title' => 'Painel Admin',
    'user' => [
        'name' => $_SESSION['user_id'] ?? 'Usuário',
        'role' => $_SESSION['user_role'] ?? 'N/A'
    ],
    'menu' => $sidebarMenu ?? [],
    'logout' => [
        'label' => 'Sair',
        'href' => '/logout',
        'class' => 'block text-center text-red-600 hover:underline font-semibold'
    ]
];

// Renderiza header
echo BlockRenderer::render('Header', [
    'title' => 'Painel Admin - CoreCRM',
    'logo' => '<a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM Admin</a>',
    'user' => $sidebar['user'],
    'actions' => [
        ['label' => 'Sair', 'href' => '/logout', 'class' => 'bg-red-500 hover:bg-red-600']
    ],
    'sidebar' => $sidebar // Passa sidebar para o HeaderBlock se desejar customizar
]);

echo '<div class="min-h-screen flex bg-gradient-to-br from-blue-50 to-indigo-100">';
// Se quiser renderizar a sidebar como bloco separado, pode usar BlockRenderer::render('Sidebar', ...)
// echo BlockRenderer::render('Sidebar', $sidebar);

echo '<main class="flex-1 p-10 flex flex-col gap-6">';
echo '<h1 class="text-2xl font-bold text-indigo-800 mb-4">Dashboard</h1>';
echo '<div class="space-y-6">';

// Renderiza widgets do painel admin
WidgetHandler::render_widgets('admin_panel');

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

echo '</div>';
echo '</main>';
echo '</div>';

// Renderiza footer
echo BlockRenderer::render('Footer', []);
?>
