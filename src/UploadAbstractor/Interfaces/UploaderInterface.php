<?php

/**
 * File: src/UploadAbstractor/Interfaces/UploaderInterface.php
 * 
 * Interface defining common operations for any upload driver (local or S3).
 * All methods and classes use English technical doc comments.
 */

namespace UploadAbstractor\Interfaces;

interface UploaderInterface
{
    /**
     * Create a new repository (bucket or directory).
     *
     * @param string $name Name of the repository (bucket or directory).
     * 
     * @return bool True on success, false on failure.
     */
    public function createRepository(string $name): bool;

    /**
     * Upload a file to the specified repository.
     *
     * @param string $bucket   Name of the repository (bucket or directory).
     * @param string $key      Path/key under which the file will be stored.
     * @param string $filePath Local file system path to the source file.
     * 
     * @return string|null Returns the URL or file path if successful, 
     * null on failure.
     */
    public function upload(string $bucket, string $key, string $filePath): ?string;

    /**
     * List all repositories (buckets or directories).
     *
     * @return array<string> Array of repository names.
     */
    public function listRepositories(): array;

    /**
     * List all objects (files) inside a repository (bucket or directory).
     *
     * @param string $bucket Name of the repository (bucket or directory).
     * 
     * @return array<string> Array of object keys/names.
     */
    public function listObjects(string $bucket): array;

    /**
     * Move an existing object from one key/path to another within the same 
     * repository.
     *
     * @param string $bucket         Name of the repository (bucket or directory).
     * @param string $sourceKey      Current path/key of the object to be moved.
     * @param string $destinationKey New path/key where the object will be moved.
     * 
     * @return bool True on success, false on failure.
     */
    public function move(string $bucket, string $sourceKey, string $destinationKey): bool;
}
