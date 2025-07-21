<?php
require_once __DIR__ . '/../../../../themes/default/blocks/BlockRenderer.php';

$user = [ 'name' => $_SESSION['user_id'] ?? 'Usuário' ];
$sidebarMenu = [
    'items' => [
        [ 'label' => 'Dashboard', 'href' => '/admin', 'icon' => 'fa-tachometer-alt' ],
        [ 'label' => 'Usuários', 'href' => '/usuarios', 'icon' => 'fa-users' ],
        [ 'label' => 'Plugins', 'href' => '/admin/plugins', 'icon' => 'fa-plug' ],
        [ 'label' => 'Logs', 'href' => '/admin/logs', 'icon' => 'fa-file-alt', 'class' => 'bg-indigo-100 text-indigo-700 font-semibold' ],
    ]
];
$sidebarFooter = '<a href="/admin" class="block text-center text-gray-700 hover:underline font-semibold mb-2">Retornar</a>';
$sidebarFooter .= '<a href="/logout" class="block text-center text-red-600 hover:underline font-semibold">Sair</a>';

// Header
 echo BlockRenderer::render('Header', [
    'title' => 'Logs do Sistema',
    'logo' => '<a href="/admin" class="text-xl font-bold tracking-tight hover:underline">CoreCRM Admin</a>',
    'user' => $user,
    'actions' => [
        ['label' => 'Sair', 'href' => '/logout', 'class' => 'bg-red-500 hover:bg-red-600']
    ]
]);
?>
<div class="min-h-screen flex bg-gradient-to-br from-blue-50 to-indigo-100">
    <?php echo BlockRenderer::render('Sidebar', [
        'title' => 'Administração',
        'avatar' => [ 'src' => '/public/assets/img/avatar.png', 'alt' => $user['name'] ],
        'menu' => $sidebarMenu,
        'footer' => $sidebarFooter,
        'width' => 'w-64',
        'color' => 'bg-white/90',
        'fixed' => false
    ]); ?>
    <main class="flex-1 p-10 flex flex-col gap-6">
        <div class="max-w-3xl mx-auto p-6 flex-1 w-full">
            <h1 class="text-2xl font-bold mb-4 flex items-center gap-2"><span class="material-icons">description</span>Logs do Sistema</h1>
            <div class="mb-6">
                <form method="get" action="/admin/logs" class="flex gap-2 items-center">
                    <label for="logfile" class="font-semibold">Arquivo:</label>
                    <select name="logfile" id="logfile" class="border rounded px-2 py-1">
                        <?php foreach ($logFiles as $file): ?>
                            <option value="<?php echo htmlspecialchars($file); ?>" <?php if ($file === $selectedLog) echo 'selected'; ?>><?php echo htmlspecialchars($file); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-1 rounded hover:bg-indigo-700 flex items-center gap-1"><span class="material-icons">search</span>Ver</button>
                </form>
            </div>
            <div class="bg-white rounded shadow p-4 overflow-auto max-h-[60vh]">
                <pre class="text-xs text-gray-800 whitespace-pre-wrap"><?php echo htmlspecialchars($logContent); ?></pre>
            </div>
        </div>
    </main>
</div>
<?php echo BlockRenderer::render('Footer', [
    'breadcrumbs' => true,
    'clock' => true,
    'status' => 'Admin Online',
    'content' => '&copy; ' . date('Y') . ' CoreCRM Admin'
]);
?>
