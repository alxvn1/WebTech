<?php

namespace App\Service;

use App\Model\Genre;
use App\Repository\GenreRepository;

class GenreService
{
    private GenreRepository $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    public function getAllGenres(): array
    {
        return $this->genreRepository->findAll();
    }

    public function getGenreById(int $id): ?Genre
    {
        return $this->genreRepository->findOne($id);
    }

    public function deleteGenre(int $id): bool
    {
        return $this->genreRepository->delete($id);
    }

    public function addGenre(string $name): bool
    {
        $genre = new Genre();
        $genre->name = $name;
        return $this->genreRepository->add($genre);
    }

    public function updateGenre(int $id, string $name): bool
    {
        $genre = new Genre();
        $genre->id = $id;
        $genre->name = $name;
        return $this->genreRepository->update($genre);
    }
}
