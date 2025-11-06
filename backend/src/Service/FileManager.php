<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileManager
{
    public function __construct(
        private string           $targetDirectory,
        private SluggerInterface $slugger
    )
    {
    }

    public function upload(UploadedFile $uploadedFile, User $user, Folder $folder): File
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $fileName = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();

        $userDirectory = 'user_' . $user->getId();

        $directory = $this->targetDirectory . '/' . $userDirectory;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $file = new File()
            ->setServerName("$userDirectory/$fileName")
            ->setName($uploadedFile->getClientOriginalName())
            ->setMimeType($uploadedFile->getMimeType() ?: 'application/octet-stream')
            ->setSize($uploadedFile->getSize())
            ->setUser($user)
            ->setFolder($folder);

        $uploadedFile->move($directory, $fileName);

        return $file;
    }

    public function delete(File $file): void
    {
        $filePath = $this->targetDirectory . '/' . $file->getServerName();

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}