<?php

use PHPUnit\Framework\TestCase;
use App\Upload\UploadFile;
use App\Upload\Enums\UploadDriver;

class S3UploadTest extends TestCase
{
    private string $bucket = 'php-unit-bucket';

    public function testCanCreateS3Bucket()
    {
        $upload = new UploadFile(UploadDriver::S3);
        $result = $upload->createRepository($this->bucket);
        $this->assertTrue($result);
    }

    public function testCanUploadToS3()
    {
        $upload = new UploadFile(UploadDriver::S3);
        $upload->createRepository($this->bucket);

        $filePath = tempnam(sys_get_temp_dir(), 's3upl');
        file_put_contents($filePath, 'arquivo para S3');

        $url = $upload->upload($this->bucket, 'teste-s3.txt', $filePath);
        $this->assertNotNull($url);
        $this->assertStringContainsString('teste-s3.txt', $url);
    }

    public function testCanListS3Buckets()
    {
        $upload = new UploadFile(UploadDriver::S3);
        $upload->createRepository($this->bucket);

        $buckets = $upload->listBuckets();
        $this->assertContains($this->bucket, $buckets);
    }

    public function testCanListS3Objects()
    {
        $upload = new UploadFile(UploadDriver::S3);
        $upload->createRepository($this->bucket);

        $filePath = tempnam(sys_get_temp_dir(), 's3upl');
        file_put_contents($filePath, 'arquivo no S3');

        $upload->upload($this->bucket, 'arquivo.txt', $filePath);
        $objects = $upload->listObjects($this->bucket);

        $this->assertContains('arquivo.txt', $objects);
    }
}
