<?php
class CardBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        $content = $config['content'] ?? '';
        ob_start();
        ?>
        <div class="block-card">
            <div class="block-card-title"><?= htmlspecialchars($title) ?></div>
            <div class="block-card-content"><?= $content ?></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
