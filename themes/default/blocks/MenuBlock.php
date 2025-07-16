<?php
class MenuBlock {
    public static function render($config = []) {
        $items = $config['items'] ?? [];
        ob_start();
        ?>
        <nav class="block-menu">
            <ul>
                <?php foreach ($items as $item): ?>
                    <li><a href="<?= htmlspecialchars($item['href']) ?>"><?= htmlspecialchars($item['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
        return ob_get_clean();
    }
}
