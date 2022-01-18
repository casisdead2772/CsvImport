<?php

namespace App\Service\EntityService\Message\MessageImport;

use App\Entity\ImportType;
use App\Entity\Message;
use App\Entity\MessageImport;
use App\Repository\ImportTypeRepository;
use App\Repository\MessageImportRepository;
use App\Repository\MessageRepository;
use App\Traits\EntityManagerTrait;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class MessageImportService {
    use EntityManagerTrait;

    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator) {
        $this->paginator = $paginator;
    }

    /**
     * @param string $messageId
     * @param int $type
     *
     * @return void
     */
    public function createIfNotExists(string $messageId, int $type): void {
        /** @var MessageImportRepository $messageImportRepository */
        $messageImportRepository = $this->getRepository(MessageImport::class);
        $messageImport = $messageImportRepository->findOneBy(['message' => $messageId]);

        if (!$messageImport) {
            /** @var MessageRepository $messageRepository */
            $messageRepository = $this->getRepository(Message::class);
            $message = $messageRepository->getMessageById($messageId);

            /** @var ImportTypeRepository $importTypeRepository */
            $importTypeRepository = $this->getRepository(ImportType::class);
            $typeObject = $importTypeRepository->getImportTypeById($type);

            $result = new MessageImport();
            $result->setMessage($message);
            $result->setType($typeObject);

            $this->getEntityManager()->persist($result);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Request $request
     *
     * @return PaginationInterface
     */
    public function getAllImportsWithPaginate(Request $request): PaginationInterface {
        /** @var MessageImportRepository $messageImportRepository */
        $messageImportRepository = $this->getRepository(MessageImport::class);
        $importType = $request->query->get('import_type');
        $messages = $messageImportRepository->getAllImportsWithFilterByCreated($importType);

        return $this->paginator->paginate(
            $messages,
            $request->query->getInt('page', 1),
            10
        );
    }
}
