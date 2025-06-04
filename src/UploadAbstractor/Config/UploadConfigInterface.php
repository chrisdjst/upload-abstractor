<?php

/**
 * File: src/UploadAbstractor/Config/UploadConfigInterface.php
 *
 * Interface defining methods for upload configuration providers.
 * Any class implementing this interface must return an array
 * de configurações compatível com o construtor de Aws\S3\S3Client
 * ou com o driver de armazenamento local.
 */

namespace UploadAbstractor\Config;

interface UploadConfigInterface
{
    /**
     * File: toArray
     *
     * Returns the configuration as an associative array,
     * ready to be passed to Aws\S3\S3Client or usado pelo driver local.
     *
     * @return array<string, mixed> Associative array of configuration parameters.
     */
    public function toArray(): array;
}
