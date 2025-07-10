<?php
// Migration para garantir a existência e integridade da tabela users
class SystemManagerUsersMigration {
    public static function migrate($pdo) {
        // Executa o SQL do schema users
        $sql = file_get_contents(__DIR__ . '/users_schema.sql');
        try {
            $pdo->exec($sql);
        } catch (Exception $e) {
            System::log('Erro ao executar migration de usuários: ' . $e->getMessage(), 'error');
        }
    }
}
