<?php
namespace UploadAbstractor\Drivers;

use UploadAbstractor\Interfaces\UploaderInterface;

class LocalUpload implements UploaderInterface
{
    public function __construct(
        private readonly string $basePath = '/tmp/uploads'
    ) {
        $this->ensureDirectory($this->basePath);
    }

    public function createRepository(string $name): bool
    {
        $repoPath = $this->resolvePath($name);
        return $this->ensureDirectory($repoPath);
    }

    public function upload(string $directory, string $filename, string $sourcePath): ?string
    {
        $targetDir = $this->resolvePath($directory);
        $this->ensureDirectory($targetDir);

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        return copy($sourcePath, $targetPath) ? $targetPath : null;
    }

    public function listRepositories(): array
    {
        return $this->listDirOnly($this->basePath);
    }

    public function listObjects(string $directory): array
    {
        $dirPath = $this->resolvePath($directory);
        if (!is_dir($dirPath)) {
            return [];
        }

        return $this->listFilesOnly($dirPath);
    }

    // Helpers
    private function ensureDirectory(string $path): bool
    {
        return is_dir($path) || mkdir($path, 0777, true);
    }

    private function resolvePath(string $name): string
    {
        return rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
    }

    private function listDirOnly(string $path): array
    {
        return array_values(array_filter(scandir($path), fn($item) =>
            $item !== '.' && $item !== '..' && is_dir($path . DIRECTORY_SEPARATOR . $item)
        ));
    }

    private function listFilesOnly(string $path): array
    {
        return array_values(array_filter(scandir($path), fn($item) =>
            $item !== '.' && $item !== '..' && is_file($path . DIRECTORY_SEPARATOR . $item)
        ));
    }
}
