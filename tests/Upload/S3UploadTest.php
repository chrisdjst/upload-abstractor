<?php

use PHPUnit\Framework\TestCase;
use UploadAbstractor\UploadFile;
use UploadAbstractor\Enums\UploadDriver;

class S3UploadTest extends TestCase
{
    private string $bucket = 'php-unit-bucket';

    /**
     * @covers UploadAbstractor\UploadFile
     * @covers UploadAbstractor\Drivers\S3Upload
     *
     * Tests that a new S3 bucket can be created successfully.
     * Asserts that the createRepository method returns true 
     * when the bucket is created.
     */
    public function testCanCreateS3Bucket()
    {
        $upload = new UploadFile(UploadDriver::S3);
        $result = $upload->createRepository($this->bucket);
        $this->assertTrue($result);
    }

    /**
     * @covers UploadAbstractor\UploadFile
     * @covers UploadAbstractor\Drivers\S3Upload
     */
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

    /**
     * @covers UploadAbstractor\UploadFile
     * @covers UploadAbstractor\Drivers\S3Upload
     */
    public function testCanListS3Buckets()
    {
        $upload = new UploadFile(UploadDriver::S3);
        $upload->createRepository($this->bucket);

        $buckets = $upload->listBuckets();
        $this->assertContains($this->bucket, $buckets);
    }

    /**
     * @covers UploadAbstractor\UploadFile
     * @covers UploadAbstractor\Drivers\S3Upload
     */
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
