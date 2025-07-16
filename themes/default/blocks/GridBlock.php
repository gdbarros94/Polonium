<?php
class GridBlock {
    public static function render($config = []) {
        $columns = $config['columns'] ?? 2;
        $content = $config['content'] ?? '';
        ob_start();
        ?>
        <div class="block-grid" style="display:grid;grid-template-columns:repeat(<?= (int)$columns ?>,1fr);gap:1rem;">
            <?= $content ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
