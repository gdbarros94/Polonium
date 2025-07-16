<?php
class ModalBlock {
    public static function render($config = []) {
        $id = $config['id'] ?? 'modal';
        $title = $config['title'] ?? '';
        $content = $config['content'] ?? '';
        ob_start();
        ?>
        <div id="<?= htmlspecialchars($id) ?>" class="block-modal hidden">
            <div class="block-modal-content">
                <h2><?= htmlspecialchars($title) ?></h2>
                <div><?= $content ?></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
