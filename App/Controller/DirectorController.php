<?php

namespace App\Controller;

use App\Core\TemplateEngine;
use App\Service\DirectorService;

class DirectorController
{
    private DirectorService $directorService;

    public function __construct(DirectorService $directorService)
    {
        $this->directorService = $directorService;
    }

    public function listAction(): void
    {
        $directors = $this->directorService->getAllDirectors();
        $templatePath = __DIR__ . '/../../public/views/director/director_list.html';

        $templateEngine = new TemplateEngine();
        echo $templateEngine->render($templatePath, [
            'directors' => $directors
        ]);
    }

    public function deleteAction(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo "ID не указан";
            return;
        }

        $success = $this->directorService->deleteDirector($id);

        if ($success) {
            header('Location: /directors');
        } else {
            echo "Ошибка при удалении режиссера";
        }
    }

    public function addAction(): void
    {
        $name = $_POST['name'] ?? '';

        if (empty($name)) {
            echo "Все поля обязательны для заполнения";
            return;
        }

        $success = $this->directorService->addDirector($name);

        if ($success) {
            header('Location: /directors');
        } else {
            echo "Ошибка при добавлении режиссера";
        }
    }
}