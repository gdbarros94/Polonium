<?php

class System
{
    public static function init()
    {
        // Health check
        self::healthCheck();

        // Integridade do banco de dados (será implementado com DatabaseHandler)
        // self::checkDatabaseIntegrity();

        // Gerenciamento de logs
        self::setupLogging();
    }

    private static function healthCheck()
    {
        // Implementar verificações de saúde do sistema aqui
        // Ex: permissões de pasta, extensões PHP necessárias, etc.
        error_log('System health check completed.');
    }

    function logMessage($logFile, $message)
{
    $logDir = dirname($logFile);

    // Verifica se o diretório existe, se não, cria de forma recursiva e segura
    if (!is_dir($logDir)) {
        if (!mkdir($logDir, 0755, true) && !is_dir($logDir)) {
            throw new RuntimeException("Não foi possível criar o diretório de log: $logDir");
        }
    }

    // Verifica se o arquivo existe, se não, cria
    if (!file_exists($logFile)) {
        $handle = fopen($logFile, 'w');
        if ($handle === false) {
            throw new RuntimeException("Não foi possível criar o arquivo de log: $logFile");
        }
        fclose($handle);
        // Define permissão se necessário
        chmod($logFile, 0644);
    }

    // Escreve a mensagem no log
    file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
}

    public static function log($message, $level = 'info')
    {
        // Centralizar a gestão de logs
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp][$level] $message\n";
        file_put_contents(__DIR__ . '/../logs/system.log', $logEntry, FILE_APPEND);
    }

    public static function getLogs($limit = 100)
    {
        // Retorna os últimos logs para visualização na interface /admin
        $logFile = __DIR__ . '/../logs/system.log';
        if (!file_exists($logFile)) {
            return [];
        }
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($lines, -$limit);
    }
}


