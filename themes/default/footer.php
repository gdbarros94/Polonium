<footer class="w-full h-8 bg-indigo-800 text-white flex items-center justify-between px-4 text-xs fixed bottom-0 left-0 z-50" style="height:32px;min-height:32px;max-height:32px;">
    <div id="footer-breadcrumbs" class="truncate">
        <span id="breadcrumb">Página: <span id="breadcrumb-current">Carregando...</span></span>
    </div>
    <div id="footer-status" class="flex items-center gap-4">
        <span id="footer-clock">--:--:--</span>
        <span class="hidden sm:inline" id="footer-system-status">Status: <span class="text-green-400">Online</span></span>
    </div>
    <script>
        // Relógio dinâmico
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('pt-BR');
            const date = now.toLocaleDateString('pt-BR');
            document.getElementById('footer-clock').textContent = date + ' ' + time;
        }
        setInterval(updateClock, 1000);
        updateClock();
        // Breadcrumb dinâmico
        function getCurrentPage() {
            let path = window.location.pathname;
            if (path === '/') return 'Home';
            let parts = path.split('/').filter(Boolean);
            return parts.length ? parts[parts.length-1].replace(/-/g, ' ').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Página';
        }
        document.getElementById('breadcrumb-current').textContent = getCurrentPage();
    </script>
</footer>
</body>
</html>


