<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\File;
use App\Entity\Folder;
use App\Helper\HashHelper;
use App\Repository\FileRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FileController extends BaseController
{
    public function __construct(
        readonly private EntityManagerInterface $entityManager,
        readonly private FileManager            $fileManager,
        readonly private FileRepository         $fileRepository,
    )
    {
    }

    #[Route(
        '/api/files/upload/{folder}',
        name: 'api_files_upload',
        requirements: ['folder' => '\d+'],
        methods: ['POST']
    )]
    public function upload(Request $request, Folder $folder): JsonResponse
    {
        if ($folder->getUser()->getId() !== $this->getCurrentUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $uploadedFiles = $request->files->get('files');

        if (empty($uploadedFiles)) {
            $uploadedFiles = $request->files->get('file');
            if ($uploadedFiles) {
                $uploadedFiles = [$uploadedFiles];
            } else {
                return $this->json(
                    ['error' => 'Нет файлов для загрузки'],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        $uploadedCount = 0;
        $errors = [];
        $successFiles = [];

        foreach ($uploadedFiles as $uploadedFile) {
            try {
                $file = $this->fileManager->upload($uploadedFile, $this->getCurrentUser(), $folder);

                $this->entityManager->persist($file);
                $successFiles[] = $file;
                $uploadedCount++;

            } catch (\Throwable $e) {
                $errors[] = [
                    'filename' => $uploadedFile->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        if ($uploadedCount > 0) {
            $this->entityManager->flush();
        }

        $responseData = [
            'message' => "Успешно загружено $uploadedCount файлов",
            'uploaded_count' => $uploadedCount,
            'success_files' => $successFiles,
        ];

        if (!empty($errors)) {
            $responseData['errors'] = $errors;
            $responseData['error_count'] = count($errors);
        }

        $statusCode = $uploadedCount > 0 ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;

        return $this->json($responseData, $statusCode);
    }

    #[Route(
        '/api/files/delete',
        name: 'api_file_delete',
        methods: ['DELETE']
    )]
    public function deleteFile(Request $request): JsonResponse
    {
        $fileIds = $request->toArray()['file_ids'] ?? [];

        if (empty($fileIds) || !is_array($fileIds)) {
            return $this->json(['error' => 'Не выбрано ни одного файла'], Response::HTTP_BAD_REQUEST);
        }

        $ids = array_map('intval', $fileIds);

        $files = $this->fileRepository->findBy(['id' => $ids, 'user' => $this->getCurrentUser()]);

        if (count($files) !== count($ids)) {
            throw $this->createAccessDeniedException();
        }

        foreach ($files as $file) {
            $this->fileManager->delete($file->getServerName());

            $this->entityManager->remove($file);
        }

        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    #[Route(
        '/api/files/share/{file}',
        name: 'api_file_share',
        requirements: ['file' => '\d+'],
        methods: ['POST']
    )]
    public function share(File $file): JsonResponse
    {
        if ($file->getUser()->getId() !== $this->getCurrentUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $codeCdate = new \DateTime(timezone: new \DateTimeZone('Europe/Moscow'));

        $hash = HashHelper::hash($file->getServerName() . $codeCdate->getTimestamp());

        $file->setCode($hash);
        $file->setCodeCdate($codeCdate);

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $this->json(['code' => $hash]);
    }

    #[Route(
        '/api/files/stop/sharing/{file}',
        name: 'api_files_stop_sharing',
        requirements: ['file' => '\d+'],
        methods: ['POST']
    )]
    public function stopSharing(File $file): JsonResponse
    {
        if ($file->getUser()->getId() !== $this->getCurrentUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $file->setCode(null);
        $file->setCodeCdate(null);

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}