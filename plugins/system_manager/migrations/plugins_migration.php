<?php
class SystemManagerPluginsMigration {
    public static function migrate($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS plugins (
                id INTEGER PRIMARY KEY AUTO_INCREMENT,
                slug VARCHAR(100) NOT NULL UNIQUE,
                name VARCHAR(255) NOT NULL,
                active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (Exception $e) {
            System::log('Erro ao executar migration de plugins: ' . $e->getMessage(), 'error');
        }
    }
}
