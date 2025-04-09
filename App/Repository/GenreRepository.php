<?php

namespace App\Repository;

use App\Core\EntityManager;
use App\Model\Genre;
use PDO;


class GenreRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Genre");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Genre::class);
    }

    public function findOne(int $id): ?Genre
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Genre WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$data)
        {
            return null;
        }

        $genre = new Genre();
        $genre->id = $data['id'];
        $genre->name = $data['name'];

        return $genre;
    }

    public function add(Genre $genre): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO Genre (name) VALUES (:name)"
        );
        return $stmt->execute([
            'name' => $genre->getName(),
        ]);
    }

    public function update(Genre $genre): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE Genre SET name = :name WHERE id = :id"
        );
        return $stmt->execute([
            'name' => $genre->getName(),
            'id' =>$genre->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM Genre WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
