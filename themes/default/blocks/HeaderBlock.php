<?php
class HeaderBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        ob_start();
        ?>
        <header class="block-header">
            <h1><?= htmlspecialchars($title) ?></h1>
        </header>
        <?php
        return ob_get_clean();
    }
}
