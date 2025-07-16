<?php
class FormBlock {
    public static function render($config = []) {
        $fields = $config['fields'] ?? [];
        $action = $config['action'] ?? '#';
        $method = $config['method'] ?? 'post';
        ob_start();
        ?>
        <form class="block-form" action="<?= htmlspecialchars($action) ?>" method="<?= htmlspecialchars($method) ?>">
            <?php foreach ($fields as $field): ?>
                <div class="block-form-field">
                    <label><?= htmlspecialchars($field['label']) ?></label>
                    <input type="<?= htmlspecialchars($field['type'] ?? 'text') ?>" name="<?= htmlspecialchars($field['name']) ?>" value="<?= htmlspecialchars($field['value'] ?? '') ?>" />
                </div>
            <?php endforeach; ?>
        </form>
        <?php
        return ob_get_clean();
    }
}
