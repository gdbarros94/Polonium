<?php
/**
 * HeaderBlock
 *
 * Renderiza o cabeçalho do sistema, com logo, título, menu, avatar, ações, responsividade, etc.
 *
 * Config:
 *   - title: string (título principal)
 *   - logo: string|false (HTML/logo, padrão 'CoreCRM')
 *   - menu: array (itens do menu)
 *   - user: array (dados do usuário)
 *   - actions: array (ações rápidas)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Header', [
 *     'title' => 'Dashboard',
 *     'logo' => '<a href="/">CoreCRM</a>',
 *     'user' => ['name'=>'João'],
 *     'actions' => [ ['label'=>'Logout','href'=>'/logout','class'=>'bg-red-500'] ],
 *   ]);
 */
class HeaderBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        $logo = $config['logo'] ?? '<a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM</a>';
        $menu = $config['menu'] ?? [];
        $user = $config['user'] ?? null;
        $actions = $config['actions'] ?? [];
        $extraClass = $config['class'] ?? '';
        $sidebar = $config['sidebar'] ?? null;
        ob_start();
        ?>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($title ?: 'CRM V1') ?></title>
            <!-- Tailwind CSS -->
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="/public/assets/css/blocks.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
            </style>
        </head>
        <body>
        <?php if ($sidebar): ?>
        <aside class="w-64 bg-white/90 shadow-lg p-6 flex flex-col min-h-screen float-left">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-indigo-700 mb-2"><?= htmlspecialchars($sidebar['title'] ?? 'Sidebar') ?></h2>
                <p class="text-gray-500 text-sm">Bem-vindo, <?= htmlspecialchars($sidebar['user']['name'] ?? 'Usuário') ?>!</p>
                <p class="text-gray-400 text-xs mb-2">Papel: <?= htmlspecialchars($sidebar['user']['role'] ?? 'N/A') ?></p>
            </div>
            <?php if (!empty($sidebar['menu']) && is_array($sidebar['menu'])): ?>
                <nav class="mb-4">
                    <?php foreach ($sidebar['menu'] as $item): ?>
                        <a href="<?= htmlspecialchars($item['href'] ?? '#') ?>" class="block px-3 py-2 rounded hover:bg-indigo-100 text-indigo-700 font-medium <?= $item['class'] ?? '' ?>">
                            <?php if (!empty($item['icon'])): ?><i class="fa <?= htmlspecialchars($item['icon']) ?> mr-1"></i><?php endif; ?>
                            <?= htmlspecialchars($item['label'] ?? '') ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
            <div class="mt-auto pt-8">
                <a href="<?= htmlspecialchars($sidebar['logout']['href'] ?? '/logout') ?>" class="<?= $sidebar['logout']['class'] ?? 'block text-center text-red-600 hover:underline font-semibold' ?>">
                    <?= htmlspecialchars($sidebar['logout']['label'] ?? 'Sair') ?>
                </a>
            </div>
        </aside>
        <?php endif; ?>
        <header class="block-header w-full flex items-center justify-between px-6 py-3 bg-indigo-700 shadow text-white <?= $extraClass ?>">
            <div class="flex items-center gap-4">
                <?= $logo ?>
                <?php if ($menu && is_array($menu)): ?>
                    <nav class="hidden sm:flex gap-2">
                        <?php foreach ($menu as $item): ?>
                            <a href="<?= htmlspecialchars($item['href'] ?? '#') ?>" class="px-2 py-1 rounded hover:bg-indigo-600 transition-colors <?= $item['class'] ?? '' ?>">
                                <?php if (!empty($item['icon'])): ?><i class="fa <?= htmlspecialchars($item['icon']) ?> mr-1"></i><?php endif; ?>
                                <?= htmlspecialchars($item['label'] ?? '') ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-4">
                <button id="theme-toggle-btn" onclick="toggleTheme()" style="padding:6px 12px;border-radius:6px;border:1px solid var(--color-border);background:var(--color-bg);color:var(--color-text);cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span id="theme-toggle-icon">
                        <i class="fa fa-moon"></i>
                    </span>
                </button>
                <script>
                function updateThemeIcon() {
                    var theme = document.body.getAttribute('data-theme');
                    var icon = document.getElementById('theme-toggle-icon');
                    if (theme === 'dark') {
                        icon.innerHTML = '<i class="fa fa-sun"></i>';
                    } else {
                        icon.innerHTML = '<i class="fa fa-moon"></i>';
                    }
                }
                document.addEventListener('DOMContentLoaded', updateThemeIcon);
                document.body.addEventListener('themechange', updateThemeIcon);
                window.toggleTheme = function() {
                    var current = document.body.getAttribute('data-theme');
                    var next = current === 'dark' ? 'light' : 'dark';
                    document.body.setAttribute('data-theme', next);
                    localStorage.setItem('theme', next);
                    updateThemeIcon();
                    var event = new Event('themechange');
                    document.body.dispatchEvent(event);
                };
                </script>
                <?php if ($user): ?>
                    <span class="hidden sm:inline">Olá, <?= htmlspecialchars($user['name'] ?? 'Usuário') ?></span>
                <?php endif; ?>
                <?php if ($actions && is_array($actions)):
                    foreach ($actions as $action): ?>
                        <a href="<?= htmlspecialchars($action['href'] ?? '#') ?>" class="px-3 py-1 rounded text-white font-semibold transition-colors <?= $action['class'] ?? 'bg-indigo-500 hover:bg-indigo-600' ?>">
                            <?= htmlspecialchars($action['label'] ?? '') ?>
                        </a>
                <?php endforeach; endif; ?>
            </div>
        </header>
        <?php
        return ob_get_clean();
    }
}
