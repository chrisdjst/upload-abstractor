<?php
namespace UploadAbstractor\Drivers;

use UploadAbstractor\Interfaces\UploadInterface;

class LocalUpload implements UploadInterface
{
    private string $basePath;

    public function __construct(string $basePath = '/tmp/uploads')
    {
        $this->basePath = rtrim($basePath, '/');
        if (!is_dir($this->basePath)) {
            mkdir($this->basePath, 0777, true);
        }
    }

    public function createRepository(string $name): bool
    {
        $path = "{$this->basePath}/{$name}";
        return !file_exists($path) ? mkdir($path, 0777, true) : true;
    }

    public function upload(string $bucket, string $key, string $filePath): ?string
    {
        $destDir = "{$this->basePath}/{$bucket}";
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        $destPath = "{$destDir}/{$key}";
        return copy($filePath, $destPath) ? $destPath : null;
    }

    public function listBuckets(): array
    {
        return array_values(array_filter(scandir($this->basePath), fn($item) =>
            $item !== '.' && $item !== '..' && is_dir("{$this->basePath}/{$item}")
        ));
    }

    public function listObjects(string $bucket): array
    {
        $path = "{$this->basePath}/{$bucket}";
        if (!is_dir($path)) {
            return [];
        }

        return array_values(array_filter(scandir($path), fn($item) =>
            $item !== '.' && $item !== '..' && is_file("{$path}/{$item}")
        ));
    }
}
