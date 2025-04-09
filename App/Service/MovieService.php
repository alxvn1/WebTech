<?php

namespace App\Service;

use App\Model\Movie;
use App\Repository\MovieRepository;

class MovieService
{
    private MovieRepository $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function getAllMovies(): array
    {
        return $this->movieRepository->findAll();
    }

    public function getMoviesByGenre(int $genreID): array
    {
        return $this->movieRepository->findByGenre($genreID);
    }

    public function getMovieById(int $id): ?Movie
    {
        return $this->movieRepository->findOne($id);
    }

    public function deleteMovie(int $id): bool
    {
        return $this->movieRepository->delete($id);
    }

    public function addMovie(string $title, float $rating, int $genreID): bool
    {
        $movie = new Movie();
        $movie->title = $title;
        $movie->rating = $rating;
        $movie->genreID = $genreID;
        return $this->movieRepository->add($movie);
    }

    public function updateMovie(int $id, string $title, string $rating, int $genreID): bool
    {
        $movie = new Movie();
        $movie->id = $id;
        $movie->title = $title;
        $movie->rating = $rating;
        $movie->genreID = $genreID;
        return $this->movieRepository->update($movie);
    }
}