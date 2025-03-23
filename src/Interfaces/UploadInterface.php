<?php
namespace UploadAbstractor\Interfaces;

interface UploadInterface
{
    public function createRepository(string $name): bool;
    public function upload(string $bucket, string $key, string $filePath): ?string;
    public function listRepositories(): array;
    public function listObjects(string $bucket): array;
}
