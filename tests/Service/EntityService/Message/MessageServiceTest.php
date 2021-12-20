<?php

namespace App\Tests\Service\EntityService\Message;

use App\Entity\Message;
use App\Service\EntityService\Message\MessageService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageServiceTest extends KernelTestCase {
    public function testCreateMessage(): void {
        $testMessageId = uniqid('', true);
        $messageService = static::getContainer()->get(MessageService::class);
        $messageRepository = static::getContainer()->get('doctrine')->getRepository(Message::class);
        $messageService->create($testMessageId);
        $message = $messageRepository->findOneBy(['messageId' => $testMessageId]);

        self::assertEquals($testMessageId, $message->getMessageId());
    }
}
