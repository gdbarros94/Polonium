<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Carrega as configurações globais
$config = require_once __DIR__ . 
'/config/config.php';
echo "teste";
// Carrega os módulos principais
require_once __DIR__ . '/core/System.php';
require_once __DIR__ . '/core/ThemeHandler.php';
require_once __DIR__ . '/core/RoutesHandler.php';
require_once __DIR__ . '/core/HookHandler.php';
require_once __DIR__ . '/core/PluginHandler.php';
require_once __DIR__ . '/core/DatabaseHandler.php';
require_once __DIR__ . '/core/OutHandler.php';
require_once __DIR__ . '/core/APIHandler.php';

// Inicia o sistema
System::init();
ThemeHandler::init();
DatabaseHandler::init();
OutHandler::init();
PluginHandler::init();
RoutesHandler::init();
echo "teste 2";
// Dispatch da rota
RoutesHandler::dispatch();
echo "teste 3";

