<?php
// Formulário de novo/edição de usuário
ThemeHandler::render_header(['title' => 'Usuário - CoreCRM']);
$isEdit = isset($user);
?>
<div class="container mx-auto mt-10 max-w-lg">
    <h1 class="text-2xl font-bold text-indigo-800 mb-6"><?php echo $isEdit ? 'Editar Usuário' : 'Novo Usuário'; ?></h1>
    <form method="post" class="bg-white rounded shadow p-6 flex flex-col gap-4">
        <label>
            Nome:
            <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </label>
        <label>
            Email:
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </label>
        <label>
            Usuário:
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </label>
        <label>
            Tipo:
            <select name="tipo" class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="admin" <?php if(($user['tipo'] ?? '')=='admin') echo 'selected'; ?>>Admin</option>
                <option value="usuario" <?php if(($user['tipo'] ?? '')=='usuario') echo 'selected'; ?>>Usuário</option>
                <option value="moderador" <?php if(($user['tipo'] ?? '')=='moderador') echo 'selected'; ?>>Moderador</option>
            </select>
        </label>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="ativo" value="1" <?php if(($user['ativo'] ?? 1)) echo 'checked'; ?>> Ativo
        </label>
        <label>
            Senha:
            <input type="password" name="senha" class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" <?php if(!$isEdit) echo 'required'; ?>>
            <?php if($isEdit) echo '<span class="text-xs text-gray-400">Preencha para alterar a senha</span>'; ?>
        </label>
        <div class="flex gap-4 mt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"><?php echo $isEdit ? 'Salvar' : 'Criar'; ?></button>
            <a href="/usuarios" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancelar</a>
        </div>
    </form>
</div>
<?php ThemeHandler::render_footer(); ?>
