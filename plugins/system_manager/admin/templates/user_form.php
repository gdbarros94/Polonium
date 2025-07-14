<?php
            Name:
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
$isEdit = isset($user);
?>
            Role:
            <select name="role" class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="admin" <?php if(($user['role'] ?? '')=='admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if(($user['role'] ?? '')=='user') echo 'selected'; ?>>User</option>
                <option value="moderator" <?php if(($user['role'] ?? '')=='moderator') echo 'selected'; ?>>Moderator</option>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" required class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </label>
        <label>
            <input type="checkbox" name="active" value="1" <?php if(($user['active'] ?? 1)) echo 'checked'; ?>> Active
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
        </label>
            Password:
            <input type="password" name="password" class="mt-1 block w-full rounded border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" <?php if(!$isEdit) echo 'required'; ?> >
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
