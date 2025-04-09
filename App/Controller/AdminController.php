<?php

namespace App\Controller;

use App\Core\TemplateEngine;
use App\Service\AdminService;

class AdminController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function indexAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $templatePath = __DIR__ . '/../../public/views/admin/file_manager.html';
        $templateEngine = new TemplateEngine();

        $viewData = $this->adminService->getFileListViewData($relativePath);
        echo $templateEngine->render($templatePath, $viewData);
    }

    public function uploadAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $this->adminService->uploadFile($_FILES, $relativePath);
        header("Location: /admin/files?path=" . urlencode($relativePath));
    }

    public function createDirectoryAction(): void
    {
        $relativePath = $_GET['path'] ?? '';
        $this->adminService->createDirectory($_POST['directory'], $relativePath);
        header("Location: /admin/files?path=" . urlencode($relativePath));
    }

    public function downloadAction(): void
    {
        $this->adminService->downloadFile($_GET['file']);
    }

    public function deleteAction(): void
    {
        $this->adminService->deleteFile($_POST['file']);
        header("Location: /admin/files");
    }

    public function editAction(): void
    {
        try {
            $templatePath = __DIR__ . '/../../public/views/admin/edit_file.html';
            $templateEngine = new TemplateEngine();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (empty($_POST['file']) || !isset($_POST['content'])) {
                    throw new \Exception("Недостаточно данных для сохранения");
                }

                $this->adminService->editFile($_POST['file'], $_POST['content']);

                $_SESSION['success'] = "Файл успешно сохранён";
                header("Location: /admin/files");
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (empty($_GET['file'])) {
                    throw new \Exception("Не указан файл для редактирования");
                }

                error_log("Requested file: " . $_GET['file']); // Логирование

                $viewData = $this->adminService->getEditFileViewData($_GET['file']);
                echo $templateEngine->render($templatePath, $viewData);
            }
        } catch (\Exception $e) {
            error_log("Error in editAction: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header("Location: /admin/files");
            exit;
        }
    }
}