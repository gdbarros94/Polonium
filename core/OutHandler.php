
<?php

class OutHandler
{
    public static function init()
    {
        // Iniciar sessão
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        System::log("OutHandler initialized. Session started.");
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION["user_id"]);
    }

    public static function requireAuth()
    {
        if (!self::isLoggedIn()) {
            self::redirect("/login");
        }
    }

    public static function checkPermission($permission)
    {
        // Implementar lógica de permissões aqui
        // Por enquanto, apenas um placeholder
        System::log("Checking permission: {$permission}", "debug");
        if (self::isLoggedIn() && isset($_SESSION["user_role"])) {
            // Exemplo simples: admin tem todas as permissões
            if ($_SESSION["user_role"] === "admin") {
                return true;
            }
            // Lógica mais complexa de ACL viria aqui, talvez lendo de um banco de dados
            // Por exemplo, verificar se a permissão está associada ao papel do usuário
            return false; // Por padrão, nega
        }
        return false;
    }

    public static function login($userId, $userRole)
    {
        $_SESSION["user_id"] = $userId;
        $_SESSION["user_role"] = $userRole;
        System::log("User {$userId} logged in with role {$userRole}.");
    }

    public static function logout()
    {
        session_unset();
        session_destroy();
        System::log("User logged out.");
        self::redirect("/login");
    }

    public static function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }
}


