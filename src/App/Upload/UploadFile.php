<?php
namespace App\Upload;

use App\Upload\Enums\UploadDriver;
use App\Upload\Interfaces\UploadInterface;
use App\Upload\Drivers\S3Upload;
use App\Upload\Drivers\LocalUpload;

class UploadFile
{
    private UploadInterface $driver;

    public function __construct(UploadDriver $option)
    {
        $this->driver = match ($option) {
            UploadDriver::S3 => new S3Upload(),
            UploadDriver::LOCAL => new LocalUpload()
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

    public function listBuckets(): array
    {
        return $this->driver->listBuckets();
    }

    public function listObjects(string $bucket): array
    {
        return $this->driver->listObjects($bucket);
    }
}
