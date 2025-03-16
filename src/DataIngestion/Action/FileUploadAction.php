<?php
namespace App\DataIngestion\Action;

use App\DataIngestion\Domain\FileUploader;
use App\DataIngestion\Responder\FileUploadResponder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

readonly class FileUploadAction
{
    public function __construct(private FileUploader $fileUploader, private FileUploadResponder $responder)
    {
    }

    #[Route('/api/upload-file', name: 'file_upload', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $file = $request->files->get('file');
        if (!$file) {
            return $this->responder->respondWithError("No file provided.");
        }

        try {
            $filename = $this->fileUploader->upload($file);
        } catch (FileException $e) {
            return $this->responder->respondWithError("File upload error: " . $e->getMessage());
        }

        return $this->responder->respondWithData(['filename' => $filename]);
    }
}
