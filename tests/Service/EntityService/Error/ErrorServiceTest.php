<?php

namespace App\Tests\Service\EntityService\Error;

use App\Entity\Error;
use App\Entity\Message;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use App\Service\EntityService\Error\ErrorService;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ErrorServiceTest extends KernelTestCase {
    /**
     * @var ObjectManager
     */
    private ObjectManager $entityManager;

    /**
     * @var MessageRepository|MockObject
     */
    private $messageRepositoryMock;

    /**
     * @var ErrorRepository|MockObject
     */
    private $errorRepositoryMock;

    /**
     * @var ErrorService|MockObject
     */
    private $errorServiceMock;

    protected function setUp(): void {
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
        $this->messageRepositoryMock = $this->createMock(MessageRepository::class);
        $this->errorRepositoryMock = $this->createMock(ErrorRepository::class);

        $this->errorServiceMock = $this->createPartialMock(ErrorService::class, ['getRepository']);

        $this->errorServiceMock
            ->method('getRepository')
            ->willReturnMap([
                [Error::class, $this->errorRepositoryMock],
                [Message::class, $this->messageRepositoryMock],
            ]);
    }

    public function testGetUnsuitedByMessage(): void {
        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById')
            ->willReturn(new Message());

        $this->errorRepositoryMock->expects($this->once())
            ->method('getUnsuitedByMessage');

        $this->errorServiceMock->getUnsuitedMessage('test id');
    }

    public function testGetFailureMessage(): void {
        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById');

        $this->errorRepositoryMock->expects($this->once())
            ->method('getFailureByMessage');

        $this->errorServiceMock->getFailureMessage('test id');
    }

    public function testGetLastMessageError(): void {
        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById')
            ->willReturn(new Message());

        $errorMock = $this->createMock(Error::class);
        $errorMock
            ->method('getErrorMessage')
            ->willReturn('test failed: fail');
        $this->errorRepositoryMock->expects($this->once())
            ->method('getLastErrorByMessage')
            ->willReturn($errorMock);

        $this->errorServiceMock->getLastMessageError('test id');
    }

    protected function tearDown(): void {
        $this->entityManager->getConnection()->rollback();
        parent::tearDown();
    }
}
