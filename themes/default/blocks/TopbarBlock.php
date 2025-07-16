<?php
class TopbarBlock {
    public static function render($config = []) {
        $content = $config['content'] ?? '';
        ob_start();
        ?>
        <div class="block-topbar">
            <?= $content ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
