<?php

namespace App\Controller;

use App\Service\EntityService\ErrorService\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController {
    /**
     * @Route("errors/{id}", name="import_error", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @param mixed $id
     * @param ErrorService $errorService
     */
    public function show($id, ErrorService $errorService): JsonResponse {
        try {
            $errorMessage = $errorService->getLastErrorMessage($id);
        } catch (BadRequestException $e) {

            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->json($errorMessage, Response::HTTP_OK);
    }
}
