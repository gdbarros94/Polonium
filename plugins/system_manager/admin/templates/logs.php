<?php
ThemeHandler::render_header(['title' => 'Logs do Sistema']);
?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col">
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
</div>
<?php ThemeHandler::render_footer(); ?>
