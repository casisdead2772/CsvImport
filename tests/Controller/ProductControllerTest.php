<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase {
    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    protected function setUp(): void {
        $this->client = static::createClient();
    }

    /**
     * @return void
     */
    public function testIndex(): void {
        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
    }
}
