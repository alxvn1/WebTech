<?php

namespace App\Model;

class Genre
{
    public int $id;
    public string $name;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}