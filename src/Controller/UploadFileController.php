<?php

namespace App\Controller;

use App\Form\UploadFormType;
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
     * @Route ("/upload_file", name="upload", methods={"POST"})
     *
     * @param Request $request
     * @param FileUploadService $fileUploader
     * @param KernelInterface $kernel
     *
     * @return RedirectResponse|Response
     */
    public function upload(Request $request, FileUploadService $fileUploader, KernelInterface $kernel) {
        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['upload_file']->getData();
            $fileName = $fileUploader->upload($uploadedFile);
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput([
                    'command' => 'app:import',
                    'filename' => $fileName,
                ]);
            $output = new BufferedOutput();

            try {
                $application->run($input, $output);
                $this->addFlash('success', 'File imported!');
            } catch (Exception $e) {
                return new Response($e->getMessage());
            }
        } else {
            $this->addFlash('danger', (string)$form->getErrors(true, true));
        }

        return $this->redirectToRoute('upload_file');
    }
}
