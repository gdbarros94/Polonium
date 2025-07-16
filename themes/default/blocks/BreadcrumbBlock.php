<?php
class BreadcrumbBlock {
    public static function render($config = []) {
        $items = $config['items'] ?? [];
        ob_start();
        ?>
        <nav class="block-breadcrumb">
            <?php foreach ($items as $i => $item): ?>
                <a href="<?= htmlspecialchars($item['href']) ?>" class="block-breadcrumb-item">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
                <?php if ($i < count($items) - 1): ?>
                    <span class="block-breadcrumb-sep">/</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <?php
        return ob_get_clean();
    }
}
