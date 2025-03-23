<?php
namespace UploadAbstractor;

use UploadAbstractor\Enums\UploadDriver;
use UploadAbstractor\Interfaces\UploadInterface;
use UploadAbstractor\Drivers\S3Upload;
use UploadAbstractor\Drivers\LocalUpload;
use UploadAbstractor\Support\EnvManager;

class UploadFile
{
    private UploadInterface $driver;

    public function __construct(UploadDriver $option, ?EnvManager $env = null)
    {
        $env ??= EnvManager::createWithDefaultReaders();

        $this->driver = match ($option) {
            UploadDriver::S3 => new S3Upload($env),
            UploadDriver::LOCAL => new LocalUpload(),
        };
    }

    public function getDriver(): UploadInterface
    {
        return $this->driver;
    }

    public function createRepository(string $name): bool
    {
        return $this->driver->createRepository($name);
    }

    public function upload(string $bucket, string $key, string $filePath): ?string
    {
        return $this->driver->upload($bucket, $key, $filePath);
    }

    public function listRepositories(): array
    {
        return $this->driver->listRepositories();
    }

    public function listObjects(string $bucket): array
    {
        return $this->driver->listObjects($bucket);
    }
}
