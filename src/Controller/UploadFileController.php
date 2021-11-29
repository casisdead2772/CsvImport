<?php

namespace App\Controller;

use App\Form\UploadFormType;
use App\Service\FileUploadService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class UploadFileController extends AbstractController {
    /**
     * @Route("/upload_file", name="upload_file", methods={"GET"})
     */
    public function index(): Response {
        $form = $this->createForm(UploadFormType::class);

        return $this->render('index.html.twig', [
            'controller_name' => 'UploadFileController',
            'upload_form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/upload_file", name="upload", methods={"POST"})
     */
    public function upload(Request $request, FileUploadService $fileUploader, KernelInterface $kernel) {
        $form = $this->createForm(UploadFormType::class);
        $form->handleRequest($request);
        $uploadedFile = $form['upload_file']->getData();

        if ($uploadedFile) {
            $uploadedFile = $fileUploader->upload($uploadedFile);
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:import',
            'filename' => $uploadedFile,
        ]);
        $output = new BufferedOutput();

        try {
            $application->run($input, $output);
        } catch (\Exception $e) {
            $errors = $e->getMessage();
        }

        $content = $output->fetch();

        return new Response($content);
    }
}
