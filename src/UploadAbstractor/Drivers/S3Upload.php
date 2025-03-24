<?php

namespace UploadAbstractor\Drivers;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use UploadAbstractor\Interfaces\UploaderInterface;
use EnvManager\EnvManager;

class S3Upload implements UploaderInterface
{
    private S3Client $client;
    private EnvManager $env;

    public function __construct(?EnvManager $env = null)
    {
        $this->env = $env ?? new EnvManager();
        $this->client = new S3Client($this->buildConfig());
    }

    private function shouldUseIam(): bool
    {
        return $this->env->get('USE_IAM', 'false') === 'true';
    }

    private function getCredentialsIfApplicable(): ?array
    {
        if ($this->shouldUseIam()) {
            return null;
        }

        $key = $this->env->get('AWS_ACCESS_KEY_ID', 'test');
        $secret = $this->env->get('AWS_SECRET_ACCESS_KEY', 'test');

        return $key && $secret ? [
            'key' => $key,
            'secret' => $secret,
        ] : null;
    }

    private function buildConfig(): array
    {
        $region = $this->env->get('AWS_REGION', 'us-east-1');
        $deploy = $this->env->get('DEPLOY', 'false') === 'true';

        $config = [
            'version' => '2006-03-01',
            'region'  => $region,
            'endpoint' => $this->env->get('AWS_ENDPOINT', 'http://localhost:4566'),
        ];

        if ($deploy) {
            $config['use_path_style_endpoint'] = true;
        }

        if ($credentials = $this->getCredentialsIfApplicable()) {
            $config['credentials'] = $credentials;
        }

        return $config;
    }

    public function createRepository(string $bucket): bool
    {
        try {
            $this->client->createBucket(['Bucket' => $bucket]);
            $this->client->waitUntil('BucketExists', ['Bucket' => $bucket]);
            return true;
        } catch (AwsException | \Throwable $e) {
            error_log("Erro ao criar bucket: " . $e->getMessage());
            return false;
        }
    }

    public function upload(string $bucket, string $key, string $filePath): ?string
    {
        try {
            $result = $this->client->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $filePath,
            ]);
            return $result['ObjectURL'] ?? null;
        } catch (\Throwable $e) {
            error_log("Erro ao enviar arquivo: " . $e->getMessage());
            return null;
        }
    }

    public function listRepositories(): array
    {
        try {
            $result = $this->client->listBuckets();
            return array_map(fn($bucket) => $bucket['Name'], $result['Buckets'] ?? []);
        } catch (\Throwable $e) {
            error_log("Erro ao listar buckets: " . $e->getMessage());
            return [];
        }
    }

    public function listObjects(string $bucket): array
    {
        try {
            $result = $this->client->listObjectsV2(['Bucket' => $bucket]);
            return array_map(fn($object) => $object['Key'], $result['Contents'] ?? []);
        } catch (\Throwable $e) {
            error_log("Erro ao listar objetos: " . $e->getMessage());
            return [];
        }
    }
}
