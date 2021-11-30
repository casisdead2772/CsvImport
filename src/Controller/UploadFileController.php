<?php

namespace App\Controller;

use App\Form\UploadFormType;
use App\Service\CommandCallService;
use App\Service\FileUploadService;
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
     * @param CommandCallService $commandCallService
     * @param KernelInterface $kernel
     *
     * @return RedirectResponse|Response
     */
    public function upload(Request $request, FileUploadService $fileUploader, CommandCallService $commandCallService, KernelInterface $kernel) {
        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['upload_file']->getData();
            $fileName = $fileUploader->upload($uploadedFile);
            try {
                $commandResult = $commandCallService->importCsvDB($fileName);
                switch ($commandResult) {
                    case 0:
                        $this->addFlash('success', 'File successfully uploaded and imported');

                        break;
                    case 1:
                        $this->addFlash('danger', 'Error writing to database');

                        break;
                    case 2:
                        $this->addFlash('danger', 'Invalid File');

                        break;
                }
            } catch (Exception $e) {
                return new Response($e->getMessage());
            }
        } else {
            $this->addFlash('danger', (string)$form->getErrors(true, true));
        }

        return $this->redirectToRoute('upload_file');
    }
}
