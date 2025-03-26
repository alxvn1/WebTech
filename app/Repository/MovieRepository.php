<?php

namespace App\Repository;

use App\Database\DatabaseHandler;
use App\Model\Movie;

class MovieRepository
{
    private DatabaseHandler $dbHandler;

    public function __construct(DatabaseHandler $dbHandler)
    {
        $this->dbHandler = $dbHandler;
    }

    public function findAll(): array
    {
        $moviesData = $this->dbHandler->find('movies');
        return array_map([$this, 'hydrate'], $moviesData);
    }

    public function findByTitle(string $title): array
    {
        $moviesData = $this->dbHandler->find('movies', ['title' => $title]);
        return array_map([$this, 'hydrate'], $moviesData);
    }

    private function hydrate(array $data): Movie
    {
        $movie = new Movie();
        // Set properties from $data
        return $movie;
    }
}