<?php
/**
 * Plugin Name: WhatsApp Integration
 * Description: Plugin para integração com WhatsApp.
 * Version: 1.0
 * Author: Seu Nome
 */

RoutesHandler::addRoute("POST", "/whatsapp/webhook", function() {
    include 'webhook.php';
});

RoutesHandler::addRoute("GET", "/whatsapp/webhook", function() {
    http_response_code(405); // Método não permitido
    echo json_encode(['error' => 'Méthod not allowed']);
});