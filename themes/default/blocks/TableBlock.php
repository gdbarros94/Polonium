<?php
class TableBlock {
    public static function render($config = []) {
        // Exemplo básico de tabela, para expandir depois
        $columns = $config['columns'] ?? [];
        $data = $config['data'] ?? [];
        ob_start();
        ?>
        <div class="block-table-wrapper">
            <table class="block-table">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?= htmlspecialchars($col['label'] ?? $col['key']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <td><?= htmlspecialchars($row[$col['key']] ?? '') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
}
