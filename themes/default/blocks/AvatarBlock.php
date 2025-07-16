<?php
class AvatarBlock {
    public static function render($config = []) {
        $src = $config['src'] ?? '';
        $alt = $config['alt'] ?? 'Avatar';
        $size = $config['size'] ?? 40;
        ob_start();
        ?>
        <img class="block-avatar" src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($alt) ?>" style="width:<?= (int)$size ?>px;height:<?= (int)$size ?>px;border-radius:50%;object-fit:cover;" />
        <?php
        return ob_get_clean();
    }
}
