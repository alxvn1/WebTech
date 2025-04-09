<?php

namespace App\Service;

class AdminService
{
    private string $baseDir;
    private array $allowedExtensions = ['css', 'html', 'js', 'png', 'jpeg', 'jpg', 'txt'];

    public function __construct()
    {
        $this->baseDir = realpath(__DIR__ . '/../../public') . '/';
        if (!is_dir($this->baseDir)) {
            throw new \RuntimeException("Base directory is not existing: " . $this->baseDir);
        }
    }

    public function getFileListViewData(string $relativePath): array
    {
        $sanitizedPath = $this->sanitizePath($relativePath);
        $rawFiles = $this->listFiles($relativePath);

        $files = array_map(function ($file) use ($relativePath) {
            $fullPath = ($relativePath ? $relativePath . '/' : '') . $file;
            $isDir = is_dir($this->getFullPath($fullPath, true));

            return [
                'name' => $file,
                'fullPath' => $fullPath,
                'isDir' => $isDir,
                'url' => urlencode($fullPath),
                'class' => $isDir ? 'dir-item' : 'file-item',
            ];
        }, $rawFiles);

        $backPath = $relativePath ? urlencode(dirname($relativePath)) : null;

        return [
            'files' => $files,
            'currentPath' => $relativePath,
            'currentPathEncoded' => urlencode($relativePath),
            'backPath' => $backPath,
        ];
    }

    public function listFiles(string $relativePath = ''): array
    {
        $path = $this->sanitizePath($relativePath);
        $items = scandir($path);

        if ($items === false) {
            return [];
        }

        $filteredItems = [];
        foreach (array_diff($items, ['.', '..']) as $item) {
            if (stripos($item, 'admin') !== false) {
                continue;
            }

            $fullPath = $path . $item;
            if (is_dir($fullPath)) {
                $filteredItems[] = $item;
            } elseif ($this->isAllowedExtension($item)) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }

    public function uploadFile(array $file, string $relativePath = ''): void
    {
        if (!isset($file['file']) || $file['file']['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Error uploading file.");
        }

        if (!$this->isAllowedExtension($file['file']['name'])) {
            throw new \Exception("File type not allowed");
        }

        $targetDir = $this->sanitizePath($relativePath);
        $destination = $targetDir . basename($file['file']['name']);

        if (!move_uploaded_file($file['file']['tmp_name'], $destination)) {
            throw new \Exception("Error saving file.");
        }
    }

    public function createDirectory(string $directoryName, string $relativePath): void
    {
        $basePath = $this->sanitizePath($relativePath);
        $directoryName = basename($directoryName);
        $dirPath = $basePath . DIRECTORY_SEPARATOR . $directoryName;

        if (!is_dir($dirPath)) {
            if (!mkdir($dirPath, 0777, true) && !is_dir($dirPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" could not be created', $dirPath));
            }
        }
    }

    public function downloadFile(string $relativePath): void
    {
        try {
            // Get the full sanitized path first
            $fullPath = $this->getFullPath($relativePath);
            $filename = basename($fullPath);

            // Check if allowed before proceeding
            if (!$this->isAllowedExtension($filename) && !is_dir($fullPath)) {
                throw new \Exception("File type not allowed");
            }

            if (!file_exists($fullPath)) {
                throw new \Exception("File not found: " . $filename);
            }

            // Clear output buffer to prevent corruption
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set appropriate headers
            header('Content-Description: File Transfer');
            header('Content-Type: ' . mime_content_type($fullPath));
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($fullPath));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            // Output the file
            readfile($fullPath);
            exit;
        } catch (\Exception $e) {
            throw new \Exception("Download failed: " . $e->getMessage());
        }
    }

    private function deleteDirectory(string $dir): void
    {
        $fullPath = rtrim($dir, '/\\');

        if (!file_exists($fullPath)) return;
        if (!is_dir($fullPath)) throw new \Exception("Path is not a directory");

        $items = scandir($fullPath);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $fullPath . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }

        @rmdir($fullPath) or throw new \Exception("Failed to remove directory");
    }

    public function deleteFile(string $relativePath): void
    {
        $fullPath = $this->getFullPath($relativePath, allowDirectories: true);
        $filename = basename($fullPath);

        if (!file_exists($fullPath)) {
            throw new \Exception("File or directory not found: " . $filename);
        }

        if (is_dir($fullPath)) {
            $this->deleteDirectory($fullPath);
            return;
        }

        if (!$this->isAllowedExtension($filename)) {
            throw new \Exception("File type not allowed: " . $filename);
        }

        if (!unlink($fullPath)) {
            throw new \Exception("Failed to delete file: " . $filename);
        }
    }

    public function getEditFileViewData(string $relativePath): array
    {
        error_log("Trying to open file: " . $relativePath); // Логируем запрос

        $fullPath = $this->getFullPath($relativePath);
        error_log("Resolved full path: " . $fullPath); // Логируем полный путь

        $content = $this->getFileContent($fullPath);

        return [
            'fileContent' => $content,
            'filename' => basename($fullPath),
            'fileParam' => $relativePath,
        ];
    }

    public function editFile(string $relativePath, string $content): void
    {
        $fullPath = $this->resolveFilePath($relativePath);

        // Проверка возможности записи
        if (!is_writable($fullPath)) {
            throw new \Exception("Write to file is banned: " . basename($relativePath));
        }

        if (file_put_contents($fullPath, $content) === false) {
            throw new \Exception("Failed to write to file: " . basename($relativePath));
        }
    }

    private function getFullPath(string $relativePath, bool $allowDirectories = false): string
    {
        $relativePath = str_replace($this->baseDir, '', $relativePath);
        $relativePath = ltrim($relativePath, '/\\');

        $fullPath = $this->baseDir . $relativePath;
        $realPath = realpath($fullPath);

        if ($realPath === false) {
            throw new \Exception("Файл не найден: " . htmlspecialchars($relativePath));
        }

        if (!file_exists($realPath)) {
            throw new \Exception("Файл не существует: " . htmlspecialchars($relativePath));
        }

        if (is_dir($realPath) && !$allowDirectories) {
            throw new \Exception("Это директория, а не файл: " . htmlspecialchars($relativePath));
        }

        if (!is_readable($realPath)) {
            throw new \Exception("Нет прав на чтение файла: " . htmlspecialchars($relativePath));
        }

        return $realPath;
    }

    public function getFileContent(string $fullPath): string
    {
        $content = file_get_contents($fullPath);
        if ($content === false) {
            throw new \Exception("Не удалось прочитать файл");
        }
        return $content;
    }

    private function resolveFilePath(string $relativePath): string
    {
        // Нормализация пути
        $relativePath = ltrim($relativePath, '/\\');
        $fullPath = $this->baseDir . $relativePath;
        $realPath = realpath($fullPath);

        // Проверка существования
        if ($realPath === false) {
            throw new \Exception("File not found: " . basename($relativePath));
        }

        // Проверка безопасности
        if (strpos($realPath, $this->baseDir) !== 0) {
            throw new \Exception("Access denied: Path is outside allowed directory");
        }

        // Проверка, что это файл
        if (is_dir($realPath)) {
            throw new \Exception("Cannot edit directory: " . basename($relativePath));
        }

        return $realPath;
    }

    private function sanitizePath(string $relativePath): string
    {
        if (empty($relativePath)) {
            return $this->baseDir;
        }

        // Normalize path separators
        $relativePath = str_replace('\\', '/', $relativePath);
        $relativePath = ltrim($relativePath, '/');

        $fullPath = rtrim($this->baseDir, '/') . '/' . $relativePath;

        // Resolve any ../ or ./ in the path
        $realPath = realpath($fullPath) ?: $fullPath;

        // Normalize for comparison
        $normalizedBase = rtrim(str_replace('\\', '/', $this->baseDir), '/') . '/';
        $normalizedReal = rtrim(str_replace('\\', '/', $realPath), '/') . '/';

        if (strpos($normalizedReal, $normalizedBase) !== 0) {
            throw new \Exception("Access denied: Path '{$relativePath}' is outside the public directory.");
        }

        return $normalizedReal;
    }

    private function isAllowedExtension(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $this->allowedExtensions) && ($filename !== 'admin');
    }
}
