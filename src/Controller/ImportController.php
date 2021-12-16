<?php

namespace App\Controller;

use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController {
    /**
     * @Route("/import/errors/{id}", name="import_error", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @param mixed $id
     * @param ErrorService $errorService
     */
    public function showMessageError($id, ErrorService $errorService): JsonResponse {
        try {
            $errorMessage = $errorService->getLastMessageError($id);
        } catch (NotFoundHttpException $e) {
            //
            return $this->json($e->getMessage(), Response::HTTP_NO_CONTENT);
        }

        return $this->json($errorMessage, Response::HTTP_OK);
    }

    /**
     * @Route ("/import/result/{id}", "import_result", methods={"GET"})
     *
     * @param string $id
     * @param MessageService $messageService
     *
     * @return JsonResponse
     */
    public function showMessageStatus(string $id, MessageService $messageService): JsonResponse {
        //
        return $this->json($messageService->getStatusMessage($id));
    }

    /**
     * @Route ("/import/failure/{id}", "import_result", methods={"GET"})
     *
     * @param string $id
     * @param ErrorService $errorService
     *
     * @return JsonResponse
     */
    public function showImportFailures(string $id, ErrorService $errorService): JsonResponse {
        $errorMessage = $errorService->getFailureMessage($id);
        $unsuitedMessage = $errorService->getUnsuitedMessage($id);

        return $this->json([
            'errors' => unserialize($errorMessage, ['allowed_classes' => false]),
            'unsuited' => unserialize($unsuitedMessage, ['allowed_classes' => false])
        ]);
    }
}
