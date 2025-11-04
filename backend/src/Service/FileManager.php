<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    public function __construct(
        private readonly string           $targetDirectory,
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function upload(UploadedFile $uploadedFile, User $user, Folder $folder): File
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $fileName = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();

        $userDirectory = $this->targetDirectory . '/user_' . $user->getId();
        if (!is_dir($userDirectory)) {
            mkdir($userDirectory, 0777, true);
        }

        $file = new File()
            ->setServerName($fileName)
            ->setName($uploadedFile->getClientOriginalName())
            ->setMimeType($uploadedFile->getMimeType() ?: 'application/octet-stream')
            ->setSize($uploadedFile->getSize())
            ->setUser($user)
            ->setFolder($folder);

        $uploadedFile->move($userDirectory, $fileName);

        return $file;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}