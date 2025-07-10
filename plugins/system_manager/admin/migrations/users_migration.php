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
        
        // Insere usuário administrador padrão (apenas se não existir)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $adminHash = password_hash('admin123', PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, tipo, username, ativo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                'Administrador',
                'admin@corecrm.com',
                $adminHash,
                'admin',
                'admin',
                1
            ]);
        }
        // Insere usuário comum padrão (apenas se não existir)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'user'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $userHash = password_hash('user123', PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, tipo, username, ativo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                'Usuário',
                'user@corecrm.com',
                $userHash,
                'user',
                'user',
                1
            ]);
        }
    }
}
