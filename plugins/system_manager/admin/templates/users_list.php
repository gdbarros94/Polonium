<?php
require_once __DIR__ . '/../../../../themes/default/blocks/BlockRenderer.php';

$user = [ 'name' => $_SESSION['user_id'] ?? 'Usuário' ];
$sidebarMenu = [
    'items' => [
        [ 'label' => 'Dashboard', 'href' => '/admin', 'icon' => 'fa-tachometer-alt' ],
        [ 'label' => 'Usuários', 'href' => '/usuarios', 'icon' => 'fa-users', 'class' => 'bg-indigo-100 text-indigo-700 font-semibold' ],
        [ 'label' => 'Plugins', 'href' => '/admin/plugins', 'icon' => 'fa-plug' ],
        [ 'label' => 'Logs', 'href' => '/admin/logs', 'icon' => 'fa-file-alt' ],
    ]
];
$sidebarFooter = '<a href="/admin" class="block text-center text-gray-700 hover:underline font-semibold mb-2">Retornar</a>';
$sidebarFooter .= '<a href="/logout" class="block text-center text-red-600 hover:underline font-semibold">Sair</a>';

// Header
 echo BlockRenderer::render('Header', [
    'title' => 'Usuários - CoreCRM',
    'logo' => '<a href="/admin" class="text-xl font-bold tracking-tight hover:underline">CoreCRM Usuários</a>',
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
        <div class="container mx-auto mt-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-indigo-800">Usuários</h1>
                <a href="/usuarios/novo" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Novo Usuário</a>
            </div>
            <table class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Username</th>
                        <th class="py-2 px-4">Role</th>
                        <th class="py-2 px-4">Name</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">Criado em</th>
                        <th class="py-2 px-4">Atualizado em</th>
                        <th class="py-2 px-4">Ativo</th>
                        <th class="py-2 px-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="justify-center itens-center text-center border-b hover:bg-indigo-50">
                        <td class="justify-center py-2 px-4"><?php echo $user['id']; ?></td>
                        <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['name']); ?></td>
                        <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['updated_at']); ?></td>
                        <td class="justify-center py-2 px-4"><?php echo $user['active'] ? 'Yes' : 'No'; ?></td>
                        <td class="justify-center itens-center content-center py-2 px-4 flex gap-2">
                            <a href="/usuarios/editar/<?php echo $user['id']; ?>" class="align-middle text-center h-8 w-15 bg-yellow-400 text-white rounded my-auto content-center hover:bg-yellow-500 px-2 py-1 ">Editar</a>
                            <form method="post" action="/usuarios/apagar/<?php echo $user['id']; ?>" onsubmit="return confirm('Tem certeza que deseja apagar este usuário?');" class="inline my-auto">
                                <button type="submit" class="align-middle text-center h-8 w-15 bg-red-400 text-white rounded my-auto content-center hover:bg-red-500 px-2 py-1 ">Apagar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
