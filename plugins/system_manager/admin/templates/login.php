<?php
// Renderiza o header do tema, já incluindo o Tailwind via CDN no header.php do tema
ThemeHandler::render_header(['title' => 'Login - CoreCRM']);
?>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto bg-white/90 rounded-2xl shadow-2xl p-8 flex flex-col items-center relative">
        <div id="logo-block" class="mb-6 cursor-pointer select-none transition-transform duration-300 hover:scale-105">
            <svg id="corecrm-logo" xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-indigo-600" fill="none" viewBox="0 0 48 48" stroke="currentColor" stroke-width="2">
                <circle cx="24" cy="24" r="22" stroke="currentColor" stroke-width="4" fill="#6366f1"/>
                <text x="50%" y="56%" text-anchor="middle" fill="white" font-size="18" font-family="Arial" dy=".3em">CRM</text>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-indigo-700 mb-2">Bem-vindo ao <span class="text-indigo-500">CoreCRM</span></h2>
        <p class="text-gray-500 mb-6 text-center">Acesse o painel administrativo e gerencie seu negócio com segurança e praticidade.</p>
        <form method="post" action="/login" class="w-full flex flex-col gap-4">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect ?? '/admin'); ?>">
            <label class="block">
                <span class="text-gray-700">Usuário</span>
                <input type="text" id="user" name="user" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Digite seu usuário">
            </label>
            <label class="block">
                <span class="text-gray-700">Senha</span>
                <input type="password" id="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Digite sua senha">
            </label>
            <button type="submit" class="mt-2 w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition-colors">Entrar</button>
        </form>
        <?php if (!empty($error)) { echo '<p class="mt-4 text-red-600 text-center">' . htmlspecialchars($error) . '</p>'; } ?>
        <div class="mt-8 text-xs text-gray-400 text-center">
            &copy; <?php echo date('Y'); ?> CoreCRM. Todos os direitos reservados.<br>
            <span class="italic">Desenvolvido com <span class="text-pink-500">&#10084;&#65039;</span> para pessoas e negócios.</span>
        </div>
        <!-- Easter egg: mensagem oculta -->
        <div id="easteregg" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-white/95 rounded-2xl z-50">
            <div class="text-4xl mb-2 animate-bounce">🥚</div>
            <div class="text-lg font-bold text-indigo-700">Parabéns! Você encontrou o easter egg 🎉</div>
            <div class="text-sm text-gray-500 mt-2">A criatividade é o segredo do sucesso!</div>
            <button onclick="document.getElementById('easteregg').classList.add('hidden');" class="mt-4 px-4 py-2 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-700">Fechar</button>
        </div>
    </div>
    <script>
        // Easter egg: clique 5x no logo para ativar
        let logo = document.getElementById('logo-block');
        let count = 0;
        logo.addEventListener('click', function() {
            count++;
            if(count === 5) {
                document.getElementById('corecrm-logo').classList.add('egg');
                document.getElementById('easteregg').classList.remove('hidden');
                count = 0;
            }
            setTimeout(() => { count = 0; }, 2000);
        });
    </script>
<?php ThemeHandler::render_footer(); ?>
