<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Database\DatabaseHandler;
use App\Database\EntityManager;

class MovieController
{
    private MovieRepository $movieRepository;

    public function __construct()
    {
        $entityManager = new EntityManager();
        $dbHandler = new DatabaseHandler($entityManager->getConnection());
        $this->movieRepository = new MovieRepository($dbHandler);
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../../public/views/{$view}.html";
    }

    public function index(): void
    {
        $movies = $this->movieRepository->findAll();
        $this->render('movies', ['movies' => $movies]);
    }
    public function view(): void
    {
        // Implementation for single movie view
    }
}