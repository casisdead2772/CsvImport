<?php

namespace App\Tests\Service\EntityService\Error;

use App\Entity\Error;
use App\Entity\Message;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use App\Service\EntityService\Error\ErrorService;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ErrorServiceTest extends KernelTestCase {
    /**
     * @var MessageRepository|MockObject
     */
    private MockObject|MessageRepository $messageRepositoryMock;

    /**
     * @var ErrorRepository|MockObject
     */
    private ErrorRepository|MockObject $errorRepositoryMock;

    /**
     * @var ErrorService|MockObject
     */
    private ErrorService|MockObject $errorServiceMock;

    /**
     * @var MockObject|Error
     */
    private MockObject|Error $errorMock;

    protected function setUp(): void {
        $this->messageRepositoryMock = $this->createMock(MessageRepository::class);
        $this->errorRepositoryMock = $this->createMock(ErrorRepository::class);
        $this->errorMock = $this->createMock(Error::class);

        $this->errorServiceMock = $this->createPartialMock(ErrorService::class, ['getRepository']);

        $this->errorMock->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn(serialize(['test result']));

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
            ->method('getUnsuitedByMessage')
            ->willReturn($this->errorMock);

        $this->errorServiceMock->getUnsuitedMessage('test id');
    }

    public function testGetFailureMessage(): void {
        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById');

        $this->errorRepositoryMock->expects($this->once())
            ->method('getFailureByMessage')
            ->willReturn($this->errorMock);

        $this->errorServiceMock->getFailureMessage('test id');
    }

    public function testGetLastMessageError(): void {
        $messageMock = $this->createMock(Message::class);

        $messageMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(1);

        $this->messageRepositoryMock->expects($this->once())
            ->method('getMessageById')
            ->willReturn($messageMock);

        $this->errorMock
            ->method('getErrorMessage')
            ->willReturn('test failed: fail');

        $this->errorRepositoryMock
            ->method('getLastErrorByMessage')
            ->willReturn($this->errorMock);

        $this->errorServiceMock->getMessageError('test id');
    }
}
