<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\FolderDTO;
use App\Entity\Folder;
use App\Helper\HashHelper;
use App\Repository\FileRepository;
use App\Repository\FolderRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class FolderController extends BaseController
{
    public function __construct(
        readonly private FolderRepository       $folderRepository,
        readonly private FileRepository         $fileRepository,
        readonly private FileManager            $fileManager,
        readonly private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/api/folder', name: 'api_folder_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] FolderDTO $folderDTO,
    ): JsonResponse
    {
        $parent = $this->folderRepository->find($folderDTO->parent_id);

        if ($parent === null) {
            return $this->json(['error' => 'Неизвестная вышестоящая папка'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getCurrentUser();

        if ($parent->getUser() !== $user) {
            return $this->json(['error' => 'Нет доступа'], Response::HTTP_FORBIDDEN);
        }

        if ($this->folderRepository->findOneBy([
                'parent' => $parent,
                'name' => $folderDTO->name
            ]) !== null) {
            return $this->json(['error' => 'Папка с таким названием уже существует'], Response::HTTP_CONFLICT);
        }

        $folder = new Folder()
            ->setParent($parent)
            ->setUser($user)
            ->setName($folderDTO->name);

        $this->entityManager->persist($folder);
        $this->entityManager->flush();

        return $this->json($folder);
    }

    #[Route(
        '/api/folder/{folder}',
        name: 'api_folder_update',
        requirements: ['folder' => '\d+'],
        methods: ['PUT'])
    ]
    public function update(Folder $folder, Request $request): JsonResponse
    {
        if ($folder->getUser()->getId() !== $this->getCurrentUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $newName = $request->toArray()['name'] ?? '';

        if (!is_string($newName)) {
            return $this->json('Некорректный формат названия папки', Response::HTTP_BAD_REQUEST);
        }

        $folder->setName($newName);

        $this->entityManager->persist($folder);
        $this->entityManager->flush();

        return $this->json($folder);
    }

    #[Route(
        '/api/folder/{folder}',
        name: 'api_folder_delete',
        requirements: ['folder' => '\d+'],
        methods: ['DELETE'])
    ]
    public function delete(Folder $folder): JsonResponse
    {
        if (
            $folder->getUser()->getId() !== $this->getCurrentUser()->getId()
            || $folder->getParent() === null
        ) {
            throw $this->createAccessDeniedException();
        }

        $fileServerNames = $this->fileRepository->getFilesForDelete($folder->getId());

        $this->entityManager->remove($folder);
        $this->entityManager->flush();

        foreach ($fileServerNames as $fileServerName) {
            $this->fileManager->delete($fileServerName);
        }

        return $this->json([], Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/folder/root', name: 'api_folder_get_root', methods: ['GET'])]
    public function getRoot(): JsonResponse
    {
        $user = $this->getCurrentUser();

        $rootFolder = $this->folderRepository->findOneBy([
            'user' => $user,
            'parent' => null,
        ]);

        if ($rootFolder === null) {
            $rootFolder = Folder::createRootFolder($user);
            $this->entityManager->persist($rootFolder);
            $this->entityManager->flush();
        } elseif ($rootFolder->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Нет доступа'], Response::HTTP_FORBIDDEN);
        }

        $foldersByParent = $this->folderRepository->findBy(['parent' => $rootFolder]);

        return $this->json([
            'root' => $rootFolder,
            'content' => $foldersByParent,
        ]);
    }

    #[Route(
        '/api/folder/{folder}',
        name: 'api_folder_get',
        requirements: ['folder' => '\d+'],
        methods: ['GET']
    )]
    public function getFolder(
        Folder                       $folder,
        #[MapQueryParameter] ?string $code
    ): JsonResponse
    {
        $accessPath = $code !== null ? $this->folderRepository->getAccessPathByCode($folder->getId(), $code) : null;

        if (
            $folder->getUser()->getId() !== $this->getCurrentUser()->getId()
            && $accessPath === null
        ) {
            throw $this->createAccessDeniedException();
        }

        $childFolders = $this->folderRepository->findBy(['parent' => $folder]);

        return $this->json([
            'folder' => $folder,
            'childFolders' => $childFolders,
            'accessPath' => $accessPath,
        ]);
    }

    #[Route(
        '/api/folder/share/{folder}',
        name: 'api_folder_share',
        requirements: ['folder' => '\d+'],
        methods: ['POST']
    )]
    public function share(Folder $folder): JsonResponse
    {
        if (
            $folder->getUser()->getId() !== $this->getCurrentUser()->getId()
            || $folder->getParent() === null
        ) {
            throw $this->createAccessDeniedException();
        }

        $codeCdate = new \DateTime(timezone: new \DateTimeZone('Europe/Moscow'));

        $hash = HashHelper::hash($folder->getName() . $codeCdate->getTimestamp());

        $folder->setCode($hash);
        $folder->setCodeCdate($codeCdate);

        $this->entityManager->persist($folder);
        $this->entityManager->flush();

        return $this->json(['code' => $hash]);
    }
}