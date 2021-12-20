<?php

namespace App\Tests\Service;

use App\Service\FileUploadService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function PHPUnit\Framework\assertFileExists;

class FileUploadServiceTest extends WebTestCase {
    /**
     * @var string
     */
    private string $filename;

    /**
     * @ORM\Column(type="string")
     */
    private $uploadedFileFromRequest;

    /**
     * @var object|null
     */
    private ?object $uploadFileService;

    protected function setUp(): void {
        parent::setUp();
        $projectDir = getcwd();
        copy($projectDir.'/storage/csvfiles/stock.csv', $projectDir.'/storage/test/csvfiles/stock.csv');
        $uploadedFile = new UploadedFile($projectDir.'/storage/test/csvfiles/stock.csv', 'stock.csv');

        $client = static::createClient();
        $client->request('POST', '/', [], [
            'upload_file' => $uploadedFile
        ]);
        $container = $client->getContainer();
        $this->uploadFileService = $container->get(FileUploadService::class);
        $this->uploadedFileFromRequest = $client->getRequest()->files->get('upload_file');
    }

    /**
     * @return void
     */
    public function testUpload(): void {
        $this->filename = $this->uploadFileService->upload($this->uploadedFileFromRequest);
        assertFileExists($this->filename);
    }

    public function tearDown(): void {
        parent::tearDown();
        unlink($this->filename);
    }
}
