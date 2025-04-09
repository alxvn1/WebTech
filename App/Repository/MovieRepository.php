<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Movie;
use App\Core\EntityManager;
use PDO;

class MovieRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Movie");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Movie::class);;
    }

    public function findByGenre(int $genreID): array
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Movie WHERE genreID = :genreID");
        $stmt->execute(['genreID' => $genreID]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, Movie::class);
    }

    public function findOne(int $id): ?Movie
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Movie WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $movie = new Movie();
        $movie->id = $data['id'];
        $movie->title = $data['title'];
        $movie->rating = $data['rating'];
        $movie->genreID = $data['genreID'];

        return $movie;
    }

    public function add(Movie $movie): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO Movie (title, rating, genreID) VALUES (:title, :rating, :genreID)"
        );
        return $stmt->execute([
            'title' => $movie->getTitle(),
            'rating' => $movie->getRating(),
            'genreID' => (int)$movie->getGenreID()
        ]);
    }

    public function update(Movie $movie): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE Movie SET title = :title, rating = :rating, genreID = :genreID WHERE id = :id"
        );
        return $stmt->execute([
            'title' => $movie->getTitle(),
            'rating' => $movie->getRating(),
            'genreID' => $movie->getGenreID(),
            'id' => $movie->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM Movie WHERE id = ?");
        return $stmt->execute([$id]);
    }
}