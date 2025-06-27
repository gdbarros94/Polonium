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

    private static function setupLogging()
    {
        // Configurar o sistema de log
        ini_set('log_errors', 'On');
        ini_set('error_log', __DIR__ . '/../logs/system.log');
        system("mkdir -p (__DIR__ . '/../logs')");
        if (!file_exists(__DIR__ . '/../logs/system.log')) {
            file_put_contents(__DIR__ . '/../logs/system.log', '');
        }
        error_log('Logging system initialized.');
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


