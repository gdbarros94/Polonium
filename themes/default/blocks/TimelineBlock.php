<?php
class TimelineBlock {
    public static function render($config = []) {
        $items = $config['items'] ?? [];
        ob_start();
        ?>
        <ul class="block-timeline">
            <?php foreach ($items as $item): ?>
                <li>
                    <span class="block-timeline-date"><?= htmlspecialchars($item['date'] ?? '') ?></span>
                    <span class="block-timeline-content"><?= htmlspecialchars($item['content'] ?? '') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }
}
