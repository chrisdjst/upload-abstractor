<?php

use PHPUnit\Framework\TestCase;
use UploadAbstractor\UploaderFile;
use UploadAbstractor\Enums\UploaderDriver;

class UploadFileTest extends TestCase
{

    /**
     * @covers UploadAbstractor\UploaderFile
     * @covers UploadAbstractor\Drivers\LocalUpload
     */
    public function testCanCreateLocalRepository()
    {
        $upload = new UploaderFile(UploaderDriver::LOCAL);
        $result = $upload->createRepository('test-bucket');
        $this->assertTrue($result);
    }

    /**
     * @covers UploadAbstractor\UploaderFile
     */
    public function testCanUploaderFileLocally()
    {
        $upload = new UploaderFile(UploaderDriver::LOCAL);
        $upload->createRepository('test-bucket');

        $tempFile = tempnam(sys_get_temp_dir(), 'upl');
        file_put_contents($tempFile, 'conteúdo de teste');

        $result = $upload->upload('test-bucket', 'teste.txt', $tempFile);
        $this->assertFileExists($result);
    }

    /**
     * @covers UploadAbstractor\UploaderFile
     * @covers UploadAbstractor\Drivers\LocalUpload
     */
    public function testCanListLocalBuckets()
    {
        $upload = new UploaderFile(UploaderDriver::LOCAL);
        $upload->createRepository('bucket1');
        $upload->createRepository('bucket2');

        $buckets = $upload->listRepositories();
        $this->assertContains('bucket1', $buckets);
        $this->assertContains('bucket2', $buckets);
    }

    /**
     * @covers UploadAbstractor\UploaderFile
     * @covers UploadAbstractor\Drivers\LocalUpload
     */
    public function testCanListLocalObjects()
    {
        $upload = new UploaderFile(UploaderDriver::LOCAL);
        $upload->createRepository('test-bucket');

        $filePath = sys_get_temp_dir() . '/exemplo.txt';
        file_put_contents($filePath, 'arquivo de exemplo');

        $upload->upload('test-bucket', 'exemplo.txt', $filePath);
        $objects = $upload->listObjects('test-bucket');

        $this->assertContains('exemplo.txt', $objects);
    }
}
