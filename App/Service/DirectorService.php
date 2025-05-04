<?php

namespace App\Service;

use App\Model\Director;
use App\Repository\DirectorRepository;

class DirectorService
{
    private DirectorRepository $directorRepository;

    public function __construct(DirectorRepository $directorRepository)
    {
        $this->directorRepository = $directorRepository;
    }

    public function getAllDirectors(): array
    {
        return $this->directorRepository->findAll();
    }

    public function getDirectorById(int $id): ?Director
    {
        return $this->directorRepository->findOne($id);
    }

    public function deleteDirector(int $id): bool
    {
        return $this->directorRepository->delete($id);
    }

    public function addDirector(string $name): bool
    {
        $director = new Director();
        $director->setName($name);
        return $this->directorRepository->add($director);
    }

    public function updateDirector(int $id, string $name): bool
    {
        $director = new Director();
        $director->id = $id;
        $director->setName($name);
        return $this->directorRepository->update($director);
    }
}