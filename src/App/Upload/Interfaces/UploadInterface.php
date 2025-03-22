<?php
namespace App\Upload\Interfaces;

interface UploadInterface
{
    public function createRepository(string $name): bool;
    public function upload(string $bucket, string $key, string $filePath): ?string;
    public function listBuckets(): array;
    public function listObjects(string $bucket): array;
}
