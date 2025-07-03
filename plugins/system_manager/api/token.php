<?php
// token.php - geração de token seguro para API do system_manager

require_once __DIR__ . '/../../core/APIHandler.php';

class SystemManagerTokenApi {
    public static function registerRoutes() {
        // POST /api/token
        RoutesHandler::addRoute("POST", "/api/token", [APIHandler::class, 'generateToken']);
    }
}
