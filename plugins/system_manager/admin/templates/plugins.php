<?php
// Template: plugins.php
// Lista de plugins instalados e formulário para upload de novo plugin
ThemeHandler::render_header(['title' => 'Plugins Instalados']);
?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
    <div class="max-w-3xl mx-auto p-6 flex-1 w-full">
        <h1 class="text-2xl font-bold mb-4 flex items-center gap-2"><span class="material-icons">extension</span>Plugins Instalados</h1>
        <div class="mb-6">
            <form action="/admin/plugins" method="post" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-2 items-center">
                <input type="file" name="plugin_zip" accept=".zip" required class="border rounded px-3 py-2" />
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 flex items-center gap-1"><span class="material-icons">upload</span>Enviar Plugin (.zip)</button>
            </form>
        </div>
        <div class="bg-white rounded shadow p-4">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="py-2">Nome</th>
                        <th class="py-2">Pasta</th>
                        <th class="py-2">Status</th>
                        <th class="py-2">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plugins as $plugin): ?>
                    <tr class="border-t">
                        <td class="py-2 font-semibold"><?php echo htmlspecialchars($plugin['name']); ?></td>
                        <td class="py-2 text-gray-600"><?php echo htmlspecialchars($plugin['folder']); ?></td>
                        <td class="py-2">
                            <span class="inline-block px-2 py-1 rounded text-xs <?php echo $plugin['active'] ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'; ?>">
                                <?php echo $plugin['active'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td class="py-2">
                            <form action="/admin/plugins/toggle" method="post" style="display:inline">
                                <input type="hidden" name="slug" value="<?php echo htmlspecialchars($plugin['folder']); ?>">
                                <input type="hidden" name="action" value="<?php echo $plugin['active'] ? 'deactivate' : 'activate'; ?>">
                                <button type="submit" class="px-3 py-1 rounded <?php echo $plugin['active'] ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?> font-semibold">
                                    <?php echo $plugin['active'] ? 'Desativar' : 'Ativar'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php ThemeHandler::render_footer(); ?>
