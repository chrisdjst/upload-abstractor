<?php

/**
 * File: src/UploadAbstractor/Drivers/LocalUpload.php
 * 
 * Local filesystem-based implementation of UploaderInterface.
 */

namespace UploadAbstractor\Drivers;

use UploadAbstractor\Interfaces\UploaderInterface;

class LocalUpload implements UploaderInterface
{
    /**
     * @var string Base path in local filesystem where repositories are stored.
     */
    private string $_basePath;

    /**
     * File: __construct
     *
     * Constructor for LocalUpload driver.
     *
     * @param string|null $_basePath Base directory path where repositories will be created.
     *                               Defaults to '/tmp/uploads' if null.
     */
    public function __construct(?string $_basePath = null)
    {
        $this->_basePath = $_basePath ?? '/tmp/uploads';
        $this->ensureDirectory($this->_basePath);
    }

    /**
     * File: createRepository
     *
     * Creates a subdirectory under base path to represent a repository.
     *
     * @param string $name Directory name to create.
     * 
     * @return bool True on success, false otherwise.
     */
    public function createRepository(string $name): bool
    {
        $dir = rtrim($this->_basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
        return $this->ensureDirectory($dir);
    }

    /**
     * File: upload
     *
     * Copies a local file into the target repository under the given key.
     *
     * @param string $bucket   Name of the repository (subdirectory).
     * @param string $key      Relative path inside repository 
     *                         (e.g. 'folder/file.txt').
     * @param string $filePath Absolute path of source file in local FS.
     * 
     * @return string|null Returns full path of uploaded file, or null on failure.
     */
    public function upload(string $bucket, string $key, string $filePath): ?string
    {
        $targetDir = $this->_basePath . DIRECTORY_SEPARATOR . $bucket . DIRECTORY_SEPARATOR . dirname($key);
        $this->ensureDirectory($targetDir);

        $targetPath = $this->_basePath . DIRECTORY_SEPARATOR . $bucket . DIRECTORY_SEPARATOR . $key;
        if (copy($filePath, $targetPath)) {
            return $targetPath;
        }
        return null;
    }

    /**
     * File: listRepositories
     *
     * Lists all subdirectories under the base path.
     *
     * @return array<string> Array of subdirectory names.
     */
    public function listRepositories(): array
    {
        return $this->listDirOnly($this->_basePath);
    }

    /**
     * File: listObjects
     *
     * Lists all file names under a specific subdirectory.
     *
     * @param string $bucket Subdirectory name.
     * 
     * @return array<string> Array of file names (not full paths).
     */
    public function listObjects(string $bucket): array
    {
        $dir = $this->_basePath . DIRECTORY_SEPARATOR . $bucket;
        return $this->listFilesOnly($dir);
    }

    /**
     * File: move
     *
     * Moves (renames) a file within the same repository.
     *
     * @param string $bucket         Name of the repository (subdirectory).
     * @param string $sourceKey      Current relative key/path of the file.
     * @param string $destinationKey New relative key/path of the file.
     * 
     * @return bool True on success, false on failure.
     */
    public function move(string $bucket, string $sourceKey, string $destinationKey): bool
    {
        $sourcePath = $this->_basePath . DIRECTORY_SEPARATOR . $bucket . DIRECTORY_SEPARATOR . $sourceKey;
        $destinationDir = $this->_basePath . DIRECTORY_SEPARATOR . $bucket . DIRECTORY_SEPARATOR . dirname($destinationKey);
        $this->ensureDirectory($destinationDir);

        $destinationPath = $this->_basePath . DIRECTORY_SEPARATOR . $bucket . DIRECTORY_SEPARATOR . $destinationKey;
        return rename($sourcePath, $destinationPath);
    }

    /**
     * File: ensureDirectory
     *
     * Creates a directory (recursive) if it does not exist.
     *
     * @param string $path Filesystem path to ensure.
     * 
     * @return bool True if directory exists or was created, false otherwise.
     */
    private function ensureDirectory(string $path): bool
    {
        if (is_dir($path)) {
            return true;
        }
        return mkdir($path, 0777, true);
    }

    /**
     * File: listDirOnly
     *
     * Returns names of subdirectories in given path.
     *
     * @param string $path Parent directory.
     * 
     * @return array<string> List of subdirectory names.
     */
    private function listDirOnly(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }
        $items = scandir($path);
        $dirs = [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
                $dirs[] = $item;
            }
        }
        return $dirs;
    }

    /**
     * File: listFilesOnly
     *
     * Returns names of files in given directory.
     *
     * @param string $path Directory path.
     * 
     * @return array<string> List of file names.
     */
    private function listFilesOnly(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }
        $items = scandir($path);
        $files = [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            if (is_file($path . DIRECTORY_SEPARATOR . $item)) {
                $files[] = $item;
            }
        }
        return $files;
    }
}
