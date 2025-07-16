<?php
class ToastBlock {
    public static function render($config = []) {
        $message = $config['message'] ?? '';
        $type = $config['type'] ?? 'info';
        ob_start();
        ?>
        <div class="block-toast block-toast-<?= htmlspecialchars($type) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
