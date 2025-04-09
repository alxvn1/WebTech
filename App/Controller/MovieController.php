<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\GenreService;
use App\Service\MovieService;
use App\Core\TemplateEngine;

class MovieController
{
    private MovieService $movieService;
    private GenreService $genreService;

    public function __construct(MovieService $movieService, GenreService $genreService)
    {
        $this->movieService = $movieService;
        $this->genreService = $genreService;
    }

    public function indexAction() {
        header("Location: /movies");
        exit;
    }

    public function listAction(): void
    {
        $movies = $this->movieService->getAllMovies();
        $genres = $this->genreService->getAllGenres();

        $templatePath = __DIR__ . '/../../public/views/movie/movie_list.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movies' => $movies,
            'genres' => $genres
        ]);
    }

    public function listByGenreAction(): void
    {
        $genreID = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$genreID) {
            echo "ID отдела не указан";
            return;
        }

        $movies = $this->movieService->getMoviesByGenre($genreID);
        $genre = $this->genreService->getGenreById($genreID);

        if (!$genre) {
            echo "There is no such genre";
            return;
        }

        $templatePath = __DIR__ . '/../../public/views/movie/movie_list.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movies' => $movies,
            'genre' => $genre
        ]);
    }

    public function deleteAction(): void
    {
        $id = $_GET['id'] ?? null;
        $genreID = $_GET['genreID'] ?? null;

        $success = $this->movieService->deleteMovie($id);

        if ($success) {
            header("Location: /movies/genre?id={$genreID}");
        } else {
            echo "Error deleting movie";
        }
    }

    public function addFormAction(): void
    {
        echo 'test';
        $genreID = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$genreID) {
            echo "Genre ID not specified";
            return;
        }

        $templatePath = __DIR__ . '/../../public/views/movie/movie_add_form.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'genreID' => $genreID
        ]);
    }

    public function addAction(): void
    {
        $title = $_POST['title'] ?? '';
        $rating = $_POST['rating'] ?? '';
        $genreID = isset($_POST['genreID']) ? (int) $_POST['genreID'] : 0;

        if (empty($title) || empty($rating) || empty($genreID)) {
            echo "All fields are required";
            return;
        }

        $success = $this->movieService->addMovie($title, $rating, $genreID);

        if ($success) {
            header("Location: /movies/genre?id={$genreID}");
            exit();
        } else {
            echo "Error inserting movie";
        }
    }

    public function editFormAction(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            echo "ID is not mentioned";
            return;
        }

        $movie = $this->movieService->getMovieById($id);
        if (!$movie) {
            echo "There is no such movie";
            return;
        }

        $genres = $this->genreService->getAllGenres();

        $templatePath = __DIR__ . '/../../public/views/movie/movie_edit_form.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movie' => $movie,
            'genres' => $genres
        ]);
    }

    public function editAction(): void
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$id) {
            echo "ID is not mentioned";
            return;
        }

        $existingMovie = $this->movieService->getMovieById($id);
        if (!$existingMovie) {
            echo "There is no such movie";
            return;
        }

        $title = $_POST['title'] ?? $existingMovie->getTitle();
        $rating = $_POST['rating'] ?? $existingMovie->getRating();
        $genreID = $_POST['genreID'] ?? $existingMovie->getGenreID();

        if (empty($title) || empty($rating) || empty($genreID)) {
            echo "All fields are required";
            return;
        }

        $success = $this->movieService->updateMovie($id, $title, $rating, $genreID);

        if ($success) {
            header("Location: /movies/genre?id={$genreID}");
        } else {
            echo "Error updating movie";
        }
    }
}