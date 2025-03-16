<?php
namespace App\DataIngestion\Domain;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private string $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * Uploads a file and returns the new filename.
     *
     * @param UploadedFile $file
     * @return string
     * @throws FileException
     */
    public function upload(UploadedFile $file): string
    {
        $filename = uniqid('', true) . '.' . $file->guessExtension();
        $file->move($this->targetDirectory, $filename);
        return $filename;
    }
}
