<?php
// Migration para garantir a existência e integridade da tabela api_tokens
class SystemManagerApiTokensMigration {
    public static function migrate($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS api_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(128) NOT NULL,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (Exception $e) {
            System::log('Erro ao executar migration de tokens: ' . $e->getMessage(), 'error');
        }
    }
}
