<?php

namespace App\Tests\Service\EntityService\Message;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\EntityService\Message\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class MessageServiceTest extends KernelTestCase {
    /**
     * @var MessageRepository|MockObject|ObjectRepository
     */
    private ObjectRepository|MockObject|MessageRepository $messageRepositoryMock;

    /**
     * @var MessageService|MockObject
     */
    private MessageService|MockObject $messageServiceMock;

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

    public function testGetAllMessagesWithPaginate(): void {
        $messageService = self::getContainer()->get(MessageService::class);
        $paginator = $messageService->getAllMessagesWithPaginate(new Request());

        $this->assertInstanceOf(SlidingPagination::class, $paginator);
    }
}
