<?php
declare(strict_types=1);

namespace App\Model;

class Movie
{
    public int $id;
    public string $title;
    public ?int $genreID;
    public ?float $rating;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGenreID(): ?int
    {
        return $this->genreID;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setGenreID(int $genreID): void
    {
        $this->genreID = $genreID;
    }

    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }
}