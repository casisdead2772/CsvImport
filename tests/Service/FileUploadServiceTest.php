<?php

namespace App\Tests\Service;

use App\Service\FileUploadService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function PHPUnit\Framework\assertFileExists;

class FileUploadServiceTest extends WebTestCase {
    /**
     * @var string
     */
    private string $filePath;

    /**
     * @ORM\Column(type="string")
     */
    private $uploadedFileFromRequest;

    /**
     * @var object|null
     */
    private ?object $uploadFileService;

    private UploadedFile $uploadedFile;

    protected function setUp(): void {
        parent::setUp();
        $currentDirectory = getcwd().'/tests/storage/';
        $filename = 'testFile.test';
        $this->filePath = $currentDirectory.$filename;
        file_put_contents($this->filePath, 'bad test file');
        $this->uploadedFile = new UploadedFile($this->filePath, $filename);

        $client = static::createClient();
        $client->request('POST', '/', [], [
            'upload_file' => $this->uploadedFile
        ]);
        $container = $client->getContainer();
        $this->uploadFileService = $container->get(FileUploadService::class);
        $this->uploadedFileFromRequest = $client->getRequest()->files->get('upload_file');
    }

    public function testUpload(): void {
        $this->filePath = $this->uploadFileService->upload($this->uploadedFileFromRequest);
        assertFileExists($this->filePath);
    }

    public function testNotUploadedFile(): void {
        $this->expectException(FileException::class);
        $this->uploadFileService->upload($this->uploadedFile);
    }

    public function tearDown(): void {
        parent::tearDown();
        unlink($this->filePath);
    }
}
