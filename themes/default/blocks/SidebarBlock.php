<?php
class SidebarBlock {
    public static function render($config = []) {
        $content = $config['content'] ?? '';
        ob_start();
        ?>
        <aside class="block-sidebar">
            <?= $content ?>
        </aside>
        <?php
        return ob_get_clean();
    }
}
