<?php

namespace App\Controller;

use App\Message\UploadNotification;
use App\Messenger\UniqueIdStamp;
use App\Service\FileUploadService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadFileController extends AbstractController {
    /**
     * @Route("/", name="upload_file", methods={"GET"})
     *
     * @return Response
     */
    public function index(): Response {
        return $this->render('index.html.twig', [
            'controller_name' => 'UploadFileController',
        ]);
    }

    /**
     * @Route ("/upload", name="upload", methods={"POST"})
     *
     * @param Request $request
     * @param FileUploadService $fileUploader
     * @param MessageBusInterface $bus
     * @param ValidatorInterface $validatorInterface
     *
     * @return JsonResponse
     */
    public function uploadProduct(Request $request, FileUploadService $fileUploader, MessageBusInterface $bus, ValidatorInterface $validatorInterface): JsonResponse {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        $violations = $validatorInterface->validate(
            $uploadedFile,
            [
                new Assert\NotBlank([
                    'message' => 'Please select a file to upload'
                    ]),
                new Assert\File([
                    'maxSize'           => '5000000',
                    'mimeTypes'         => 'text/plain',
                    'mimeTypesMessage'  => 'Please upload a valid CSV document'
                ])
            ]
        );

        if ($violations->count() > 0) {
            //
            return $this->json($violations, 400);
        }

        try {
            $filename = $fileUploader->upload($uploadedFile);
            $uniqueIdStamp = new UniqueIdStamp();
            $id = $uniqueIdStamp->getUniqueId();
            $bus->dispatch(new UploadNotification($filename, $id), [$uniqueIdStamp]);
        } catch (InvalidArgumentException|FileException $e) {
            //
            return $this->json($e->getMessage(), 400);
        }

        return $this->json($id, Response::HTTP_OK);
    }
}
