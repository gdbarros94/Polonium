<?php
// Lista de usuários
ThemeHandler::render_header(['title' => 'Usuários - CoreCRM']);
?>
<div class="container mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-800">Usuários</h1>
        <a href="/usuarios/novo" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Novo Usuário</a>
    </div>
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Nome</th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Email</th>
                <th class="py-2 px-4">Usuário</th>
                <th class="py-2 px-4">Tipo</th>
                <th class="py-2 px-4">Role</th>
                <th class="py-2 px-4">Ativo</th>
                <th class="py-2 px-4">Active</th>
                <th class="py-2 px-4">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr class="text-center border-b hover:bg-indigo-50">
                <td class="py-2 px-4"><?php echo $user['id']; ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($user['username']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($user['name']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td class="py-2 px-4"><?php echo htmlspecialchars($user['updated_at']); ?></td>
                <td class="py-2 px-4"><?php echo $user['active'] ? 'Yes' : 'No'; ?></td>
                <td class="py-2 px-4 flex gap-2">
                    <a href="/usuarios/editar/<?php echo $user['id']; ?>" class="text-center align-middle h-8 w-12 bg-yellow-400 text-white rounded hover:bg-yellow-500">Editar</a>
                    <form method="post" action="/usuarios/apagar/<?php echo $user['id']; ?>" onsubmit="return confirm('Tem certeza que deseja apagar este usuário?');">
                        <button type="submit" class="text-center align-middle h-8 w-15 bg-red-600 text-white rounded hover:bg-red-700">Apagar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php ThemeHandler::render_footer(); ?>
