<?php

require_once __DIR__ . '/../app/Router.php';
require_once __DIR__ . '/../app/Database/EntityManager.php';

// Load environment variables
$dotenv = parse_ini_file(__DIR__ . '/../config/.env');
foreach ($dotenv as $key => $value) {
    putenv("$key=$value");
}

// Initialize database connection
$entityManager = new EntityManager();
$connection = $entityManager->getConnection();

// Initialize router
$router = new Router();

// Define routes
$router->addRoute('/movies', 'MovieController', 'index');
$router->addRoute('/movies/view', 'MovieController', 'view');

// Dispatch the request
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($path);