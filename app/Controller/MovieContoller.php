<?php

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

    public function index(): void
    {
        // Get movies from repository
        $movies = $this->movieRepository->findAll();

        // Pass to view
        require __DIR__ . '/../../views/movies.php';
    }

    public function view(): void
    {
        // Implementation for single movie view
    }
}