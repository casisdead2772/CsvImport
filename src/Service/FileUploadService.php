<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadService {
    /**
     * @var string
     */
    public string $targetDirectory;

    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @param $targetDirectory
     * @param SluggerInterface $slugger
     */
    public function __construct($targetDirectory, SluggerInterface $slugger) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function upload(UploadedFile $file): string {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->getClientOriginalExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (\Exception $e) {
            print($e->getMessage());
        }

        return $this->getTargetDirectory().$fileName;
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string {
        return $this->targetDirectory;
    }

    /**
     * @param $filePath
     *
     * @return string
     */
    public function uploadCommand($filePath): string {
        $filePathInfo = pathinfo($filePath);
        $fileName = $filePathInfo['basename'];
        copy($filePath, $this->targetDirectory.$fileName);

        return $this->targetDirectory.$fileName;
    }
}
