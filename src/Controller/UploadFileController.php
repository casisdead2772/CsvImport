<?php

namespace App\Controller;

use App\Form\UploadFormType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadFileController extends AbstractController {
    /**
     * @Route("/upload_file", name="upload_file")
     */
    public function index(): Response {
        $form = $this->createForm(UploadFormType::class);
        return $this->render('index.html.twig', [
            'controller_name' => 'UploadFileController',
            'upload_form' => $form->createView(),
        ]);

    }

    public function upload(Request $request, FileUploader $fileUploader) {
        $uploadedFile = $request->query->get('file')->getData();
        if ($uploadedFile) {
            $uploadedFile = $fileUploader->upload($uploadedFile);
        }
    }
}
