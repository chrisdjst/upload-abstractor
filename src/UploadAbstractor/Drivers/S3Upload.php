<?php

/**
 * File: src/UploadAbstractor/Drivers/S3Upload.php
 * 
 * AWS S3-based implementation of UploaderInterface.
 */

namespace UploadAbstractor\Drivers;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use UploadAbstractor\Interfaces\UploaderInterface;

class S3Upload implements UploaderInterface
{
    /**
     * @var array<string,mixed> _Configuration array to instantiate Aws\S3\S3Client.
     */
    private array $_config;

    /**
     * @var S3Client AWS S3 _client instance.
     */
    private S3Client $_client;

    /**
     * File: __construct
     *
     * Constructor for S3Upload driver.
     *
     * @param array<string,mixed> $_config _Configuration parameters 
     *                                     (region, version, credentials,
     *                                     endpoint, etc.).
     */
    public function __construct(array $_config)
    {
        $this->_config = $_config;
        $this->_client = new S3Client($this->_config);
    }

    /**
     * File: createRepository
     *
     * Creates an S3 bucket.
     *
     * @param string $bucket Name of the bucket to create.
     * 
     * @return bool True on success, false on failure.
     */
    public function createRepository(string $bucket): bool
    {
        try {
            $this->_client->createBucket(['Bucket' => $bucket]);
            $this->_client->waitUntil('BucketExists', ['Bucket' => $bucket]);
            return true;
        } catch (AwsException $e) {
            error_log($e->getMessage());
            return false;
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * File: upload
     *
     * Uploads a local file to S3 under specified bucket/key.
     *
     * @param string $bucket   Name of the bucket.
     * @param string $key      S3 object key (path inside bucket).
     * @param string $filePath Local filesystem path to the source file.
     * 
     * @return string|null Returns the ObjectURL on success, null on failure.
     */
    public function upload(string $bucket, string $key, string $filePath): ?string
    {
        try {
            $result = $this->_client->putObject(
                [
                'Bucket'     => $bucket,
                'Key'        => $key,
                'SourceFile' => $filePath,
                ]
            );
            return $result->get('ObjectURL');
        } catch (AwsException $e) {
            error_log($e->getMessage());
            return null;
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * File: listRepositories
     *
     * Lists all S3 buckets in the account.
     *
     * @return array<string> Array of bucket names.
     */
    public function listRepositories(): array
    {
        try {
            $result = $this->_client->listBuckets();
            $names = [];
            foreach ($result->get('Buckets') as $bucket) {
                $names[] = $bucket['Name'];
            }
            return $names;
        } catch (AwsException $e) {
            error_log($e->getMessage());
            return [];
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * File: listObjects
     *
     * Lists all object keys inside a given bucket.
     *
     * @param string $bucket Name of the bucket.
     * 
     * @return array<string> Array of object keys.
     */
    public function listObjects(string $bucket): array
    {
        try {
            $results = $this->_client->listObjectsV2(['Bucket' => $bucket]);
            $keys = [];
            if ($results->hasKey('Contents')) {
                foreach ($results->get('Contents') as $obj) {
                    $keys[] = $obj['Key'];
                }
            }
            return $keys;
        } catch (AwsException $e) {
            error_log($e->getMessage());
            return [];
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * File: move
     *
     * Moves (copies then deletes) an object within the same S3 bucket.
     *
     * @param string $bucket         Name of the bucket.
     * @param string $sourceKey      Current key/path of the object.
     * @param string $destinationKey New key/path for the object.
     * 
     * @return bool True on success, false on failure.
     */
    public function move(
        string $bucket, 
        string $sourceKey, 
        string $destinationKey
    ): bool {
        try {
            // Copy object to new key
            $this->_client->copyObject(
                [
                'Bucket'     => $bucket,
                'CopySource' => "{$bucket}/{$sourceKey}",
                'Key'        => $destinationKey,
                ]
            );

            // Wait until new object exists
            $this->_client->waitUntil(
                'ObjectExists', [
                    'Bucket' => $bucket,
                    'Key'    => $destinationKey,
                ]
            );

            // Delete original object
            $this->_client->deleteObject(
                [
                'Bucket' => $bucket,
                'Key'    => $sourceKey,
                ]
            );

            return true;
        } catch (AwsException $e) {
            error_log($e->getMessage());
            return false;
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
