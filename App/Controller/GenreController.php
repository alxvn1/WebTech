<?php

namespace App\Controller;

use App\Core\TemplateEngine;
use App\Service\GenreService;

class GenreController
{
    private GenreService $genreService;

    public function __construct(GenreService $genreService) {
        $this->genreService = $genreService;
    }

    public function listAction(): void
    {
        $genres = $this->genreService->getAllGenres();
        $templatePath = __DIR__ . '/../../public/views/genre/genre_list.html';

        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'genres' => $genres
        ]);
    }

    public function deleteAction(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $success = $this->genreService->deleteGenre($id);

        if ($success) {
            header('Location: /genres');
        } else {
            echo "Ошибка при удалении отдела";
        }
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';

        if (empty($name)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->genreService->addGenre($name);

        if ($success) {
            header('Location: /genres');
        } else {
            echo "Ошибка при добавлении отдела";
        }
    }
}