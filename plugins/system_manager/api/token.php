<?php
// token.php - geração de token seguro para API do system_manager

class SystemManagerTokenApi {
    public static function registerRoutes() {
        // POST /api/token
        RoutesHandler::addRoute("GET", "/api/token", [APIHandler::class, 'generateToken']);
    }
}