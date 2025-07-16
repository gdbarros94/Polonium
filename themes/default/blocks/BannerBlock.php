<?php
class BannerBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        $subtitle = $config['subtitle'] ?? '';
        $image = $config['image'] ?? '';
        ob_start();
        ?>
        <div class="block-banner" style="background-image:url('<?= htmlspecialchars($image) ?>')">
            <div class="block-banner-content">
                <h1><?= htmlspecialchars($title) ?></h1>
                <p><?= htmlspecialchars($subtitle) ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
