<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'CRM V1'; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="w-full flex items-center justify-between px-6 py-3 bg-indigo-700 shadow text-white">
        <div class="flex items-center gap-4">
            <a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM</a>
        </div>
        <div class="flex items-center gap-4">
            <?php if (AuthHandler::isLoggedIn()): ?>
                <span class="hidden sm:inline">Olá, <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'Usuário'); ?></span>
                <a href="/logout" class="px-3 py-1 bg-red-500 hover:bg-red-600 rounded text-white font-semibold transition-colors">Logout</a>
            <?php else: ?>
                <a href="/login" class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 rounded text-white font-semibold transition-colors">Login</a>
            <?php endif; ?>
        </div>
    </div>


