<?php
class TabsBlock {
    public static function render($config = []) {
        $tabs = $config['tabs'] ?? [];
        $active = $config['active'] ?? 0;
        ob_start();
        ?>
        <div class="block-tabs">
            <ul class="block-tabs-list">
                <?php foreach ($tabs as $i => $tab): ?>
                    <li class="block-tabs-tab<?= $i == $active ? ' active' : '' ?>">
                        <?= htmlspecialchars($tab['label']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="block-tabs-content">
                <?= $tabs[$active]['content'] ?? '' ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
