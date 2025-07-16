<?php
class FooterBlock {
    public static function render($config = []) {
        $content = $config['content'] ?? '&copy; ' . date('Y') . ' CoreCRM';
        ob_start();
        ?>
        <footer class="block-footer">
            <?= $content ?>
        </footer>
        <?php
        return ob_get_clean();
    }
}
