<?php
// Centralizador de blocos reutilizáveis do tema Default
class BlockRenderer {
    public static function render($block, $config = []) {
        $file = __DIR__ . '/' . ucfirst($block) . 'Block.php';
        if (file_exists($file)) {
            require_once $file;
            $class = ucfirst($block) . 'Block';
            if (class_exists($class)) {
                return $class::render($config);
            }
        }
        return "<!-- Bloco $block não encontrado -->";
    }
}
