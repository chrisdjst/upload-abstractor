<?php
namespace UploadAbstractor;

use UploadAbstractor\Enums\UploaderDriver;
use UploadAbstractor\Interfaces\UploaderInterface;
use UploadAbstractor\Drivers\S3Upload;
use UploadAbstractor\Drivers\LocalUpload;
use EnvManager\EnvManager;

class UploaderFile
{
    private UploaderInterface $driver;

    public function __construct(UploaderDriver $option, ?EnvManager $env = null)
    {
        $env ??= EnvManager::createWithDefaultReaders();

        $this->driver = match ($option) {
            UploaderDriver::S3 => new S3Upload($env),
            UploaderDriver::LOCAL => new LocalUpload(),
        };
    }

    public function getDriver(): UploaderInterface
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
