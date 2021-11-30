<?php

namespace App\Controller;

use App\Form\UploadFormType;
use App\Service\FileUploadService;
use App\Service\ProductImportService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
     * @param ProductImportService $productImportService
     * @param KernelInterface $kernel
     *
     * @return RedirectResponse|Response
     */
    public function upload(Request $request, FileUploadService $fileUploader, ProductImportService $productImportService, KernelInterface $kernel) {
        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['upload_file']->getData();
            $fileName = $fileUploader->upload($uploadedFile);

            try {
                $productArray = $productImportService->getCsvRowsAsArrays($fileName);
                $productImportService->importProductsByRules($productArray);
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('upload_file');
            }

        } else {
            $this->addFlash('danger', (string)$form->getErrors(true, true));

            return $this->redirectToRoute('upload_file');
        }
        $this->addFlash('success', 'File successfully imported');

        return $this->redirectToRoute('upload_file');
    }
}
