<?php

namespace App\Tests\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\EntityService\Message\MessageService;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageServiceTest extends KernelTestCase {
    /**
     * @var MessageRepository|ObjectRepository
     */
    private $messageRepositoryMock;

    /**
     * @var MessageService|MockObject
     */
    private $messageServiceMock;

    protected function setUp(): void {
        $this->messageRepositoryMock = $this->createMock(MessageRepository::class);

        $this->messageServiceMock = $this->createPartialMock(MessageService::class, ['getRepository']);

        $this->messageServiceMock
            ->method('getRepository')
            ->willReturn($this->messageRepositoryMock);
    }

    public function testGetStatusMessage(): void {
        $messageMock = $this->createMock(Message::class);
        $messageMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(1);
        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById')
            ->willReturn($messageMock);

        $this->messageServiceMock->getStatusMessage('');
    }
}
