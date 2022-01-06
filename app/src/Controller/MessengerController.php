<?php

namespace App\Controller;

use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\Message\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController {
    /**
     * @Route("/import/errors/{id}", name="import_error", methods={"GET"})
     *
     * @param string $id
     * @param ErrorService $errorService
     *
     * @return JsonResponse
     */
    public function showMessageError(string $id, ErrorService $errorService): JsonResponse {
        try {
            $errorMessage = $errorService->getMessageError($id);

            return $this->json($errorMessage, Response::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_NO_CONTENT);
        } catch (\Throwable) {
            return $this->json('Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            return $this->json($messageService->getStatusMessage($id));
        } catch (NotFoundHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_NO_CONTENT);
        } catch (\Throwable) {
            return $this->json('Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            $errorMessage = $errorService->getFailureMessage($id);
            $unsuitedMessage = $errorService->getUnsuitedMessage($id);

            return $this->json([
                'errors' => $errorMessage,
                'unsuited' => $unsuitedMessage,
            ]);
        } catch (NotFoundHttpException $e) {
            return $this->json($e->getMessage(), Response::HTTP_NO_CONTENT);
        } catch (\Throwable) {
            return $this->json('Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
