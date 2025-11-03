<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\FolderDTO;
use App\Entity\Folder;
use App\Repository\FolderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class FolderController extends BaseController
{
    public function __construct(
        readonly private FolderRepository       $folderRepository,
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
}