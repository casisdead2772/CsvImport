<?php

namespace App\Tests\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\EntityService\Message\MessageService;
use Doctrine\ORM\EntityManagerInterface;
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
        $this->messageServiceMock = $this->createPartialMock(MessageService::class, ['getRepository', 'getEntityManager']);

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

    public function testCreate(): void {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->messageServiceMock
            ->method('getEntityManager')
            ->willReturn($entityManagerMock);

        $entityManagerMock->expects($this->once())
            ->method('persist');

        $entityManagerMock->expects($this->once())
            ->method('flush');

        $this->messageServiceMock->create('testId');
    }

    public function testUpdate(): void {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->messageServiceMock
            ->method('getEntityManager')
            ->willReturn($entityManagerMock);

        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById');

        $entityManagerMock->expects($this->once())
            ->method('persist');

        $entityManagerMock->expects($this->once())
            ->method('flush');

        $this->messageServiceMock->update('testId', 1);
    }
}
