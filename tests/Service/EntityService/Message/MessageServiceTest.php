<?php

namespace App\Tests\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\EntityService\Message\MessageService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageServiceTest extends KernelTestCase {
    /**
     * @var string
     */
    private string $testMessageId;

    /**
     * @var MessageService|object|null
     */
    private $messageService;

    /**
     * @var MessageRepository|ObjectRepository
     */
    private $messageRepository;

    /**
     * @var ObjectManager
     */
    private ObjectManager $entityManager;

    protected function setUp(): void {
        $this->testMessageId = uniqid('', true);
        $this->messageService = static::getContainer()->get(MessageService::class);
        $this->messageRepository = static::getContainer()->get('doctrine')->getRepository(Message::class);
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
    }

    public function testCreateMessage(): void {
        $this->messageService->create($this->testMessageId);
        $message = $this->messageRepository->findOneBy(['messageId' => $this->testMessageId]);

        self::assertEquals($this->testMessageId, $message->getMessageId());
    }

    public function testCreateNonUniqueMessage(): void {
        $this->messageService->create($this->testMessageId);
        $this->expectException(UniqueConstraintViolationException::class);
        $this->messageService->create($this->testMessageId);
    }

    protected function tearDown(): void {
        $this->entityManager->getConnection()->rollback();
        parent::tearDown();
    }
}
