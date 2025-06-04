<?php

/**
 * File: src/UploadAbstractor/UploaderFile.php
 * 
 * Facade class that delegates upload-related actions to the chosen driver.
 */

namespace UploadAbstractor;

use UploadAbstractor\Interfaces\UploaderInterface;
use UploadAbstractor\Factories\UploaderDriverFactory;
use UploadAbstractor\Enums\UploaderDriver;

class UploaderFile
{
    /**
     * @var UploaderInterface Underlying driver instance (LocalUpload or S3Upload).
     */
    private UploaderInterface $_driver;

    /**
     * File: __construct
     *
     * Initializes UploaderFile by selecting the appropriate driver and configuration
     *
     * @param UploaderDriver             $driverOption Enum value indicating driver 
     *                                                 type ('S3' or 'LOCAL').
     * @param UploadConfigInterface|null $config       Configuration object if needed
     */
    public function __construct(
        UploaderDriver $driverOption, 
        ?\UploadAbstractor\Config\UploadConfigInterface $config = null
    ) {
        $this->_driver = UploaderDriverFactory::create($driverOption, $config);
    }

    /**
     * File: getDriver
     *
     * Returns the underlying driver instance.
     *
     * @return UploaderInterface
     */
    public function getDriver(): UploaderInterface
    {
        return $this->_driver;
    }

    /**
     * File: createRepository
     *
     * Delegates creation of repository to the driver.
     *
     * @param string $name Name of bucket or directory.
     * 
     * @return bool True on success, false otherwise.
     */
    public function createRepository(string $name): bool
    {
        return $this->_driver->createRepository($name);
    }

    /**
     * File: upload
     *
     * Delegates file upload to the driver.
     *
     * @param string $bucket   Name of bucket or directory.
     * @param string $key      Key/path for file location.
     * @param string $filePath Local filesystem path to source file.
     * 
     * @return string|null URL or path on success, null on failure.
     */
    public function upload(string $bucket, string $key, string $filePath): ?string
    {
        return $this->_driver->upload($bucket, $key, $filePath);
    }

    /**
     * File: listRepositories
     *
     * Delegates listing of all repositories to the driver.
     *
     * @return array<string> Array of repository names.
     */
    public function listRepositories(): array
    {
        return $this->_driver->listRepositories();
    }

    /**
     * File: listObjects
     *
     * Delegates listing of objects within a repository to the driver.
     *
     * @param string $bucket Name of bucket or directory.
     * 
     * @return array<string> Array of object keys.
     */
    public function listObjects(string $bucket): array
    {
        return $this->_driver->listObjects($bucket);
    }

    /**
     * File: move
     *
     * Delegates move operation to the driver.
     *
     * @param string $bucket         Name of bucket or directory.
     * @param string $sourceKey      Current key/path of the file.
     * @param string $destinationKey New key/path for the file.
     * 
     * @return bool True on success, false otherwise.
     */
    public function move(
        string $bucket, 
        string $sourceKey, 
        string $destinationKey
    ): bool {
        return $this->_driver->move($bucket, $sourceKey, $destinationKey);
    }
}
