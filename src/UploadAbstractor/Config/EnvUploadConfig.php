<?php

/**
 * File: src/UploadAbstractor/Config/EnvUploadConfig.php
 *
 * Implements UploadConfigInterface by reading AWS-related variables
 * from the environment via EnvManager.
 */

namespace UploadAbstractor\Config;

use UploadAbstractor\Config\UploadConfigInterface;
use EnvManager\EnvManager;

class EnvUploadConfig implements UploadConfigInterface
{
    /**
     * @var EnvManager
     */
    private EnvManager $_env;

    /**
     * File: __construct
     *
     * Constructor for EnvUploadConfig.
     *
     * @param EnvManager $env Env manager instance to read variables.
     */
    public function __construct(EnvManager $env)
    {
        $this->_env = $env;
    }

    /**
     * File: toArray
     *
     * Builds and returns the configuration array for S3Client.
     *
     * @return array<string, mixed> AWS SDK configuration.
     */
    public function toArray(): array
    {
        return $this->buildConfig();
    }

    /**
     * File: shouldUseIam
     *
     * Determines whether to rely on IAM role (EC2/ECS) instead of explicit credentials.
     *
     * @return bool True if USE_IAM is 'true', false otherwise.
     */
    private function shouldUseIam(): bool
    {
        return $this->_env->get('USE_IAM', 'false') === 'true';
    }

    /**
     * File: getCredentialsIfApplicable
     *
     * Returns credentials array (key/secret) if USE_IAM is false, or null
     * to let AWS SDK fetch IAM role credentials.
     *
     * @return array<string, string>|null
     */
    private function getCredentialsIfApplicable(): ?array
    {
        if ($this->shouldUseIam()) {
            return null;
        }

        $key    = $this->_env->get('AWS_ACCESS_KEY_ID', 'test');
        $secret = $this->_env->get('AWS_SECRET_ACCESS_KEY', 'test');

        return ($key && $secret) ? ['key' => $key, 'secret' => $secret] : null;
    }

    /**
     * File: buildConfig
     *
     * Assembles the AWS SDK configuration array based on environment variables.
     *
     * @return array<string, mixed>
     */
    private function buildConfig(): array
    {
        $region = $this->_env->get('AWS_REGION', 'us-east-1');
        $deploy = $this->_env->get('DEPLOY', 'false') === 'true';

        $config = [
            'version'  => '2006-03-01',
            'region'   => $region,
            'endpoint' => $this->_env->get('AWS_ENDPOINT', 'http://localhost:4566'),
        ];

        if ($deploy) {
            $config['use_path_style_endpoint'] = true;
        }

        if ($credentials = $this->getCredentialsIfApplicable()) {
            $config['credentials'] = $credentials;
        }

        return $config;
    }
}
