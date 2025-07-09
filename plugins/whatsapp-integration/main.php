<?php
/**
 * Plugin Name: WhatsApp Integration
 * Description: Plugin para integração com WhatsApp.
 * Version: 1.0
 * Author: Seu Nome
 */

 RoutesHandler::addRoute("GET", "/webhook", function() {
    include __DIR__ . '/plugins/whatsapp-integration/webhook.php';
});