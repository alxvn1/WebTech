<?php
declare(strict_types=1);

namespace App\Controller;

class AdminController
{
    private const ALLOWED_DIRS = [
        'views',
        'css',
        'js',
        'images'
    ];

    public function index(): void
    {
        $this->requireAuth();

        $currentDir = $_GET['dir'] ?? '';
        $basePath = __DIR__ . '/../../public/';
        $safePath = $this->validatePath($basePath, $currentDir);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest($safePath);
        }

        $files = $this->getFilesList($safePath);
        $this->renderAdminPanel($files, $currentDir);
    }

    private function requireAuth(): void
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Требуется авторизация.';
            exit;
        }

        // Сравнение с логином/паролем из .htpasswd
        $validUser = 'admin';
        $validHash = '$2y$10$Tjbzb/d.XVSGBbGpYj5zXOmm7CxWO4xCxP4cv5IxAVuaokDwqajvO'; // Хэш из .htpasswd
        $validPass = 'admin';

        if ($_SERVER['PHP_AUTH_USER'] !== $validUser ||
            !password_verify($_SERVER['PHP_AUTH_PW'], $validHash)) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Неверные данные.';
            exit;
        }
//        if ($_SERVER['PHP_AUTH_USER'] !== $validUser || $_SERVER['PHP_AUTH_PW'] !== $validPass) {
//            header('HTTP/1.0 403 Forbidden');
//            echo 'Неверные данные.';
//            exit;
//        }
    }

    private function validatePath(string $basePath, string $dir): string
    {
        $fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $dir);

        if ($fullPath === false) {
            return realpath($basePath);
        }

        foreach (self::ALLOWED_DIRS as $allowedDir) {
            $allowedPath = realpath($basePath . DIRECTORY_SEPARATOR . $allowedDir);
            if ($allowedPath && strpos($fullPath, $allowedPath) === 0) {
                return $fullPath;
            }
        }

        return realpath($basePath);
    }


    private function getFilesList(string $path): array
    {
        $files = [];
        $items = scandir($path);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $filePath = $path . DIRECTORY_SEPARATOR . $item;
            $files[] = [
                'name' => $item,
                'path' => $filePath,
                'is_dir' => is_dir($filePath),
                'size' => is_dir($filePath) ? '-' : $this->formatSize(filesize($filePath)),
                'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                'extension' => pathinfo($item, PATHINFO_EXTENSION)
            ];
        }

        return $files;
    }

    private function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2) . ' ' . $units[$index];
    }

    private function handlePostRequest(string $path): void
    {
        if (isset($_POST['delete'])) {
            $this->deleteFile($path . DIRECTORY_SEPARATOR . $_POST['file']);
        } elseif (isset($_FILES['upload'])) {
            $this->uploadFile($path);
        } elseif (isset($_POST['new_folder'])) {
            $this->createFolder($path, $_POST['folder_name']);
        } elseif (isset($_POST['edit_content'])) {
            $this->saveFileContent($path . DIRECTORY_SEPARATOR . $_POST['file'], $_POST['content']);
        }
    }

    private function deleteFile(string $filePath): void
    {
        if (is_dir($filePath)) {
            $this->deleteDirectory($filePath);
        } else {
            unlink($filePath);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function uploadFile(string $path): void
    {
        $targetFile = $path . DIRECTORY_SEPARATOR . basename($_FILES['upload']['name']);
        move_uploaded_file($_FILES['upload']['tmp_name'], $targetFile);
    }

    private function createFolder(string $path, string $name): void
    {
        mkdir($path . DIRECTORY_SEPARATOR . $name, 0755);
    }

    private function saveFileContent(string $filePath, string $content): void
    {
        file_put_contents($filePath, $content);
    }

    private function renderAdminPanel(array $files, string $currentDir): void
    {
        $breadcrumbs = $this->renderBreadcrumbs($currentDir);
        $filesHtml = $this->renderFiles($files, $currentDir);

        include __DIR__ . '/../../public/views/admin.html';
    }

    private function renderBreadcrumbs(string $path): string
    {
        if (empty($path)) return '';

        $parts = explode('/', $path);
        $breadcrumbs = '';
        $currentPath = '';

        foreach ($parts as $part) {
            if (empty($part)) continue;
            $currentPath .= "/$part";
            $breadcrumbs .= " / <a href=\"/admin?dir=" . urlencode(ltrim($currentPath, '/')) . "\">$part</a>";
        }

        return $breadcrumbs;
    }

    private function renderFiles(array $files, string $currentDir): string
    {
        $html = '';

        // Add parent directory link
        if (!empty($currentDir)) {
            $parentDir = dirname($currentDir);
            $parentDir = $parentDir === '.' ? '' : $parentDir;
            $html .= '<tr>
                <td><a href="/admin?dir=' . urlencode($parentDir) . '">..</a></td>
                <td>Directory</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>';
        }

        foreach ($files as $file) {
            $filePath = str_replace(__DIR__ . '/../../public/', '', $file['path']);
            $filePath = urlencode($filePath);

            $actions = '';
            if ($file['is_dir']) {
                $link = '/admin?dir=' . urlencode(ltrim($currentDir . '/' . $file['name'], '/'));
                $html .= "<tr>
                    <td><a href=\"$link\">{$file['name']}</a></td>
                    <td>Directory</td>
                    <td>-</td>
                    <td>{$file['modified']}</td>
                    <td class=\"actions\">
                        <form method=\"post\" onsubmit=\"return confirm('Are you sure you want to delete this folder?');\">
                            <input type=\"hidden\" name=\"dir\" value=\"$currentDir\">
                            <input type=\"hidden\" name=\"file\" value=\"{$file['name']}\">
                            <button type=\"submit\" name=\"delete\" class=\"btn btn-danger\">Delete</button>
                        </form>
                    </td>
                </tr>";
            } else {
                $downloadLink = '/public/' . str_replace(__DIR__ . '/../../public/', '', $file['path']);
                $editLink = '/admin?file=' . $filePath;

                $preview = '';
                if (in_array(strtolower($file['extension']), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $preview = "<a href=\"$downloadLink\" target=\"_blank\" class=\"btn btn-primary\">Preview</a>";
                }

                $html .= "<tr>
                    <td>{$file['name']}</td>
                    <td>{$file['extension']}</td>
                    <td>{$file['size']}</td>
                    <td>{$file['modified']}</td>
                    <td class=\"actions\">
                        $preview
                        <a href=\"$downloadLink\" download class=\"btn btn-primary\">Download</a>
                        <a href=\"/admin/edit?file=$filePath\" class=\"btn btn-primary edit-file\" data-file=\"{$file['name']}\">Edit</a>
                        <form method=\"post\" onsubmit=\"return confirm('Are you sure you want to delete this file?');\">
                            <input type=\"hidden\" name=\"dir\" value=\"$currentDir\">
                            <input type=\"hidden\" name=\"file\" value=\"{$file['name']}\">
                            <button type=\"submit\" name=\"delete\" class=\"btn btn-danger\">Delete</button>
                        </form>
                    </td>
                </tr>";
            }
        }

        return $html;
    }
}