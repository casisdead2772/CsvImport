<?php

namespace App\Controller;

use App\Service\EntityService\Error\ErrorService;
use App\Service\EntityService\ImportType\ImportTypeService;
use App\Service\EntityService\Message\MessageImport\MessageImportService;
use App\Service\EntityService\Message\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ImportResultController extends AbstractController {
    /**
     * @Route("/import_results", name="imports", methods={"GET"})
     *
     * @param Request $request
     * @param MessageImportService $messageImportService
     * @param ImportTypeService $importTypeService
     *
     * @return Response
     */
    public function index(Request $request, MessageImportService $messageImportService, ImportTypeService $importTypeService): Response {
        $messages = $messageImportService->getAllImportsWithPaginate($request);
        $importTypes = $importTypeService->getAllImportTypes();

        return $this->render('import/importResults.html.twig', [
            'messages' => $messages,
            'importTypes' => $importTypes
        ]);
    }

    /**
     * @Route("/import_results/{id}", name="import_results", methods={"GET"})
     *
     * @param String $id
     * @param MessageService $messageService
     * @param ErrorService $errorService
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function show(string $id, MessageService $messageService, ErrorService $errorService): Response {
        $message = $messageService->getMessage($id);
        $error = $errorService->getMessageError($message->getId());

        return $this->render('import/importErrorsList.html.twig', [
            'error' => $error,
            'message' => $message,
        ]);
    }

    /**
     * @Route("/import_results/{id}/failures", name="failures", methods={"GET"})
     *
     * @param string $id
     * @param Request $request
     * @param ErrorService $errorService
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function showImportFailures(string $id, Request $request, ErrorService $errorService): Response {
        $failures = $errorService->getMessageFailuresWithPaginate($id, $request);

        return $this->render('import/importData/failed.html.twig', [
            'failures' => $failures,
        ]);
    }

    /**
     * @Route("/import_results/{id}/unsuited", name="unsuited", methods={"GET"})
     *
     * @param string $id
     * @param Request $request
     * @param ErrorService $errorService
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function showImportUnsuited(string $id, Request $request, ErrorService $errorService): Response {
        $unsuited = $errorService->getMessageUnsuitedWithPaginate($id, $request);

        return $this->render('import/importData/unsuited.html.twig', [
            'unsuited' => $unsuited,
        ]);
    }
}
