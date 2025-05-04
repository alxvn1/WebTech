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

    public function getMoviesByDirector(int $directorID): array
    {
        return $this->movieRepository->findByDirector($directorID);
    }

    public function getMovieById(int $id): ?Movie
    {
        return $this->movieRepository->findOne($id);
    }

    public function deleteMovie(int $id): bool
    {
        return $this->movieRepository->delete($id);
    }

    public function addMovie(string $title, float $rating, int $genreID, int $directorID): bool
    {
        $movie = new Movie();
        $movie->title = $title;
        $movie->rating = $rating;
        $movie->genreID = $genreID;
        $movie->directorID = $directorID;
        return $this->movieRepository->add($movie);
    }

    public function updateMovie(int $id, string $title, float $rating, int $genreID, int $directorID): bool
    {
        $movie = new Movie();
        $movie->id = $id;
        $movie->title = $title;
        $movie->rating = $rating;
        $movie->genreID = $genreID;
        $movie->directorID = $directorID;
        return $this->movieRepository->update($movie);
    }
}