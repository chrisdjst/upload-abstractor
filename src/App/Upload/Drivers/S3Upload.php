<?php
namespace App\Upload\Drivers;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use App\Upload\Interfaces\UploadInterface;

class S3Upload implements UploadInterface
{
    private S3Client $client;

    public function __construct(string $region = 'us-east-1')
    {
        $this->client = new S3Client($this->buildConfig());
    }


    private function shouldUseIam(): bool
    {
        return Env::get('USE_IAM', 'false') === 'true';
    }

    private function getCredentialsIfApplicable(): ?array
    {
        if ($this->shouldUseIam()) {
            return null;
        }

        $key = Env::get('AWS_ACCESS_KEY_ID', 'test');
        $secret = Env::get('AWS_SECRET_ACCESS_KEY', 'test');

        if ($key && $secret) {
            return [
                'key' => $key,
                'secret' => $secret,
            ];
        }

        return null;
    }

    /**
     * Monta dinamicamente o array de configuração para o S3Client.
     *
     * @return array
     */
    private function buildConfig(): array
    {
        $region = Env::get('AWS_REGION', 'us-east-1');
        $deploy = Env::get('DEPLOY', 'false') === 'true';

        $config = [
            'version' => '2006-03-01',
            'region'  => $region,
        ];

        if (!$deploy) {
            $config['use_path_style_endpoint'] = true;
            $config['endpoint'] = Env::get('AWS_ENDPOINT');

            $key = Env::get('AWS_ACCESS_KEY_ID');
            $secret = Env::get('AWS_SECRET_ACCESS_KEY');

            if ($key && $secret) {
                $config['credentials'] = [
                    'key'    => $key,
                    'secret' => $secret,
                ];
            }
        }else{
            $config['endpoint'] = Env::get('AWS_ENDPOINT', 'http://localstack:4566');
            if(!Env::get('USE_IAM'))
            $key = Env::get('AWS_ACCESS_KEY_ID', 'test');
            $secret = Env::get('AWS_SECRET_ACCESS_KEY', 'test');
        }

        return $config;
    }

    /**
     * Cria um bucket S3.
     *
     * @param string $bucket nome do bucket a ser criado
     * @return bool true se o bucket for criado com sucesso, false caso contrário
     */

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

    public function listBuckets(): array
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
