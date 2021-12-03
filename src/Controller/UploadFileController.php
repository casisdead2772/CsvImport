<?php

namespace App\Controller;

use App\Form\UploadFormType;
use App\Service\FileUploadService;
use App\Service\ImportService\GeneralImportService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadFileController extends AbstractController {
    /**
     * @Route("/", name="upload_file", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response {
        $form = $this->createForm(UploadFormType::class);

        return $this->render('index.html.twig', [
            'controller_name' => 'UploadFileController',
            'upload_form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/", name="upload", methods={"POST"})
     *
     * @param Request $request
     * @param FileUploadService $fileUploader
     * @param GeneralImportService $productImportService
     *
     * @return RedirectResponse|Response
     */
    public function uploadProduct(Request $request, FileUploadService $fileUploader, GeneralImportService $productImportService) {
        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['upload_file']->getData();

            try {
                $fileName = $fileUploader->upload($uploadedFile);
                $productImportService->importByRules($fileName);
                $this->addFlash('success', 'File successfully imported');
            } catch (InvalidArgumentException|FileException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        } else {
            $this->addFlash('danger', (string)$form->getErrors(true, true));
        }

        return $this->redirectToRoute('upload_file');
    }
}
