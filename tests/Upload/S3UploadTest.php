<?php

use PHPUnit\Framework\TestCase;
use UploadAbstractor\UploaderFile;
use UploadAbstractor\Enums\UploaderDriver;
use UploadAbstractor\Factories\UploaderDriverFactory;
use UploadAbstractor\Config\ArrayUploadConfig;

class S3UploadTest extends TestCase
{
    private string $bucket = 'php-unit-bucket';
    private ArrayUploadConfig $config;
    private ?string $tempFile = null;

    protected function setUp(): void
    {
        parent::setUp();
        // Configuração compartilhada para os testes S3
        $this->config = new ArrayUploadConfig([
            'region' => 'us-east-1',
            'endpoint' => 'http://localhost:4566',
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => 'test',
                'secret' => 'test',
            ]
        ]);
    }

    protected function tearDown(): void
    {
        // Remove arquivo temporário, se existir
        if ($this->tempFile !== null && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    /**
     * Cria e retorna uma instância de UploaderFile usando o driver S3.
     */
    private function createS3Upload(): UploaderFile
    {
        $driver = UploaderDriverFactory::create(UploaderDriver::S3, $this->config);
        return new UploaderFile($driver);
    }

    /**
     * Testa se o bucket é criado com sucesso.
     */
    public function testCanCreateS3Bucket()
    {
        $upload = $this->createS3Upload();
        $result = $upload->createRepository($this->bucket);
        $this->assertTrue($result, "O bucket deve ser criado com sucesso.");
    }

    /**
     * Testa o upload de um arquivo para o S3.
     */
    public function testCanUploadToS3()
    {
        $upload = $this->createS3Upload();
        $upload->createRepository($this->bucket);

        // Cria arquivo temporário para upload
        $this->tempFile = tempnam(sys_get_temp_dir(), 's3upl');
        file_put_contents($this->tempFile, 'arquivo para S3');

        $url = $upload->upload($this->bucket, 'teste-s3.txt', $this->tempFile);
        $this->assertNotNull($url, "A URL retornada não deve ser nula.");
        $this->assertStringContainsString('teste-s3.txt', $url, "A URL deve conter o nome do arquivo.");
    }

    /**
     * Testa se o bucket criado é listado entre os buckets existentes.
     */
    public function testCanListS3Buckets()
    {
        $upload = $this->createS3Upload();
        $upload->createRepository($this->bucket);

        $buckets = $upload->listRepositories();
        $this->assertContains($this->bucket, $buckets, "O bucket deve estar presente na lista de buckets.");
    }

    /**
     * Testa se os objetos dentro do bucket são listados corretamente.
     */
    public function testCanListS3Objects()
    {
        $upload = $this->createS3Upload();
        $upload->createRepository($this->bucket);

        $this->tempFile = tempnam(sys_get_temp_dir(), 's3upl');
        file_put_contents($this->tempFile, 'arquivo no S3');

        $upload->upload($this->bucket, 'arquivo.txt', $this->tempFile);
        $objects = $upload->listObjects($this->bucket);

        $this->assertContains('arquivo.txt', $objects, "A lista de objetos deve conter 'arquivo.txt'.");
    }
}
