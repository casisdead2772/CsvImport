<?php

namespace App\Tests\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImportControllerTest extends WebTestCase {
    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    protected function setUp(): void {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testShowMessageStatus(): void {
        $messageRepository = $this->client->getContainer()->get('doctrine')->getRepository(Message::class);
        $messages = $messageRepository->findAll();
        $messageId = $messages[0]->getMessageId();
        $this->client->request('GET', '/import/result/'.$messageId);

        self::assertResponseIsSuccessful();
    }
}
