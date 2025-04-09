<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\EntityManager;
use App\Core\Router;
use Dotenv\Dotenv;


// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/config/');
$dotenv->load();

// Initialize database connection
$entityManager = new EntityManager($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
//$entityManager = new EntityManager('localhost', 'movie_db', 'admin', 'admin');

// Initialize router
$router = new Router($entityManager);

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_URI']);
