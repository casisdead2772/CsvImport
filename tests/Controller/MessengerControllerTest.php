<?php

namespace App\Tests\Controller;

use App\Service\EntityService\Message\MessageService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessengerControllerTest extends WebTestCase {


    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * @var ObjectManager
     */
    private ObjectManager $entityManager;

    protected function setUp(): void {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
    }

    /**
     * @return void
     */
    public function testShowMessageStatus(): void {
        $testMessageId = uniqid('', true);
        $messageService = static::getContainer()->get(MessageService::class);
        $messageService->create($testMessageId);
        $this->client->request('GET', '/import/result/'.$testMessageId);

        self::assertResponseIsSuccessful();
    }

    protected function tearDown(): void {
        $this->entityManager->getConnection()->rollback();
        parent::tearDown();
    }
}
