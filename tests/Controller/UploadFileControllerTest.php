<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UploadFileControllerTest extends WebTestCase {
    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    protected function setUp(): void {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testIndex(): void {
        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
