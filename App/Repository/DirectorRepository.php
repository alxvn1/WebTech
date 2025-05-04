<?php

namespace App\Repository;

use App\Core\EntityManager;
use App\Model\Director;
use PDO;

class DirectorRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Director");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, Director::class);
    }

    public function findOne(int $id): ?Director
    {
        $stmt = $this->entityManager->getConnection()->prepare("SELECT * FROM Director WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$data)
        {
            return null;
        }

        $director = new Director();
        $director->id = $data['id'];
        $director->name = $data['name'];

        return $director;
    }

    public function add(Director $director): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "INSERT INTO Director (name) VALUES (:name)"
        );
        return $stmt->execute([
            'name' => $director->getName(),
        ]);
    }

    public function update(Director $director): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare(
            "UPDATE Director SET name = :name WHERE id = :id"
        );
        return $stmt->execute([
            'name' => $director->getName(),
            'id' => $director->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->entityManager->getConnection()->prepare("DELETE FROM Director WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}