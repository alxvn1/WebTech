<?php
declare(strict_types=1);

namespace App\Core;

use App\Controller\AdminController;
use App\Controller\GenreController;
use App\Controller\MovieController;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Service\AdminService;
use App\Service\GenreService;
use App\Service\MovieService;

class Router
{
    private array $routes;

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->routes = [
            '/' => ['App\Controller\MovieController', 'indexAction'],

            '/genres' => ['App\Controller\GenreController', 'listAction'],
            '/genres/delete' => ['App\Controller\GenreController', 'deleteAction'],
            '/genres/add' => ['App\Controller\GenreController', 'addAction'],

//            '/movies' => ['App\Controller\MovieController', 'listAction'],
            '/movies/genre' => ['App\Controller\MovieController', 'listByGenreAction'],
            '/movies/delete' => ['App\Controller\MovieController', 'deleteAction'],
            '/movies/add-form' => ['App\Controller\MovieController', 'addFormAction'],
            '/movies/add' => ['App\Controller\MovieController', 'addAction'],
            '/movies/edit-form' => ['App\Controller\MovieController', 'editFormAction'],
            '/movies/edit' => ['App\Controller\MovieController', 'editAction'],

            '/admin' => ['App\Controller\AdminController', 'indexAction'],
            '/admin/files' => ['App\Controller\AdminController', 'indexAction'],
            '/admin/files/upload' => ['App\Controller\AdminController', 'uploadAction'],
            '/admin/files/download' => ['App\Controller\AdminController', 'downloadAction'],
            '/admin/files/delete' => ['App\Controller\AdminController', 'deleteAction'],
            '/admin/files/edit' => ['App\Controller\AdminController', 'editAction'],
            '/admin/files/directory' => ['App\Controller\AdminController', 'createDirectoryAction'],

        ];
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!array_key_exists($path, $this->routes)) {
            http_response_code(404);
            echo "Page not found";
            return;
        }

        [$controllerClass, $method] = $this->routes[$path];

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $method)) {
            http_response_code(404);
            echo "Page not found";
            return;
        }

        $controller = $this->createController($controllerClass);
        $controller->$method();
    }

    private function createController(string $controllerClass): object
    {
        switch ($controllerClass) {
            case MovieController::class:
                $movieRepository = new MovieRepository($this->entityManager);
                $movieService = new MovieService($movieRepository);
                $genreRepository = new GenreRepository($this->entityManager);
                $genreService = new GenreService($genreRepository);
                return new MovieController($movieService, $genreService);

            case GenreController::class:
                $repository = new GenreRepository($this->entityManager);
                $service = new GenreService($repository);
                return new GenreController($service);

            case AdminController::class:
                return new AdminController(new AdminService());

            default:
                throw new \RuntimeException("Unknown controller: {$controllerClass}");
        }
    }
}