<?php

namespace App\Model;

class Movie
{
    private ?int $id;
    private string $title;
    private string $genre;
    private ?string $poster;

    // Add getters and setters
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    // ... other getters and setters
}