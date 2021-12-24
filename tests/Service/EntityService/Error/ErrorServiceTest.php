<?php

namespace App\Tests\Service\EntityService\Error;

use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageService;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ErrorServiceTest extends KernelTestCase {
    /**
     * @var ErrorService|object|null
     */
    private $errorService;

    /**
     * @var ObjectManager
     */
    private ObjectManager $entityManager;

    protected function setUp(): void {
        $this->errorService = static::getContainer()->get(ErrorService::class);
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
    }

    public function testCreateErrorWithBadMessage(): void {
        $error = [
            'code' => 1,
            'message' => 'test message',
            'message_id' => ''
        ];
        $this->expectException(EntityNotFoundException::class);
        $this->errorService->create($error);
    }

    public function testCreateError(): void {
        $message_id = uniqid();
        static::getContainer()->get(MessageService::class)->create($message_id);
        $error = [
            'code' => 1,
            'message' => 'test message',
            'message_id' => $message_id
        ];
        $error = $this->errorService->create($error);
        $this->assertEquals('test message', $error->getErrorMessage());
        $this->assertEquals(1, $error->getCode());
    }

    protected function tearDown(): void {
        $this->entityManager->getConnection()->rollback();
        parent::tearDown();
    }
}
