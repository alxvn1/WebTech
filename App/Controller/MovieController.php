<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\DirectorService;
use App\Service\GenreService;
use App\Service\MovieService;
use App\Core\TemplateEngine;

class MovieController
{
    private MovieService $movieService;
    private GenreService $genreService;
    private DirectorService $directorService;

    public function __construct(MovieService $movieService, GenreService $genreService, DirectorService $directorService)
    {
        $this->movieService = $movieService;
        $this->genreService = $genreService;
        $this->directorService = $directorService;
    }

    public function indexAction() {
        header("Location: /movies");
        exit;
    }

    public function listAction(): void
    {
        $movies = $this->movieService->getAllMovies();

        $templatePath = __DIR__ . '/../../public/views/movie/movie_list.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movies' => $movies
        ]);
    }

    public function listByGenreAction(): void
    {
        $genreID = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$genreID) {
            echo "ID жанра не указан";
            return;
        }

        $movies = $this->movieService->getMoviesByGenre($genreID);
        $genre = $this->genreService->getGenreById($genreID);

        if (!$genre) {
            echo "There is no such genre";
            return;
        }

        $templatePath = __DIR__ . '/../../public/views/movie/movie_list_genre.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movies' => $movies,
            'genre' => $genre
        ]);
    }

    public function listByDirectorAction(): void
    {
        $directorID = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$directorID) {
            echo "ID режиссера не указан";
            return;
        }

        $movies = $this->movieService->getMoviesByDirector($directorID);
        $director = $this->directorService->getDirectorById($directorID);

        if (!$director) {
            echo "There is no such director";
            return;
        }

        $templatePath = __DIR__ . '/../../public/views/movie/movie_list_director.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movies' => $movies,
            'director' => $director
        ]);
    }

    public function deleteAction(): void
    {
        $id = (int) $_GET['id'] ?? null;

        $success = $this->movieService->deleteMovie($id);

        if ($success) {
            header("Location: /movies");
        } else {
            echo "Error deleting movie";
        }
    }

    public function addFormAction(): void
    {
        $genres = $this->genreService->getAllGenres();
        $directors = $this->directorService->getAllDirectors();

        $templatePath = __DIR__ . '/../../public/views/movie/movie_add_form.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'genres' => $genres,
            'directors' => $directors
        ]);
    }

    public function addAction(): void
    {
        $title = trim($_POST['title'] ?? '');
        $rating = isset($_POST['rating']) ? (float)$_POST['rating'] : 0.0;  // Явное преобразование в float
        $genreID = isset($_POST['genreID']) ? (int)$_POST['genreID'] : null;
        $directorID = isset($_POST['directorID']) ? (int)$_POST['directorID'] : null;

        if (empty($title) || empty($rating) || $genreID === null || $directorID === null) {
            echo "All fields are required and must be valid";
            return;
        }

        $success = $this->movieService->addMovie($title, $rating, $genreID, $directorID);

        if ($success) {
            header("Location: /movies");
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
        $directors = $this->directorService->getAllDirectors();

        $templatePath = __DIR__ . '/../../public/views/movie/movie_edit_form.html';
        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'movie' => $movie,
            'genres' => $genres,
            'directors' => $directors
        ]);
    }

    public function editAction(): void
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
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
        $rating = (float) $_POST['rating'] ?? $existingMovie->getRating();
        $genreID = (int) $_POST['genreID'] ?? $existingMovie->getGenreID();
        $directorID = (int) $_POST['directorID'] ?? $existingMovie->getDirectorID();

        if (empty($title) || empty($rating) || $genreID === null || $directorID === null) {
            echo "All fields are required";
            return;
        }

        $success = $this->movieService->updateMovie($id, $title, $rating, $genreID, $directorID);

        if ($success) {
            header("Location: /movies");
        } else {
            echo "Error updating movie";
        }
    }
}