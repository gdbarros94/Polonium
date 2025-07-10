<?php

// Este arquivo é o ponto de entrada principal para o seu plugin.
// Ele é carregado pelo PluginHandler quando o plugin está ativo.

// Exemplo de registro de uma rota para o plugin
RoutesHandler::addRoute("GET", "/exemplo-plugin/teste", function() {
    echo "Esta é uma página de teste do Exemplo de Plugin!";
});

// Exemplo de registro de um hook
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin executado após gerar relatório.", "info");
});

// Adiciona conteúdo ao painel admin
if (class_exists('SystemManagerAdmin')) {
    SystemManagerAdmin::addAdminContent(function() {
        echo '<div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded shadow mb-4">';
        echo '<h2 class="text-lg font-bold text-indigo-700 mb-1">Exemplo de Plugin</h2>';
        echo '<p class="text-gray-700">Este bloco foi adicionado pelo Exemplo de Plugin! Você pode usar este espaço para mostrar informações, gráficos ou ações do seu plugin.</p>';
        echo '</div>';
    });
}

System::log("Plugin Exemplo de Plugin carregado com sucesso.");



