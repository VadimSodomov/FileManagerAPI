<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\AuthDTO;
use App\Entity\Folder;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(
        readonly private UserPasswordHasherInterface $passwordHasher,
        readonly private EntityManagerInterface      $entityManager,
        readonly private JWTTokenManagerInterface    $jwtManager,
        readonly private UserRepository              $userRepository,
    )
    {
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] AuthDTO $authDTO
    ): JsonResponse
    {
        if ($this->userRepository->findOneBy(['email' => $authDTO->email]) !== null) {
            return $this->json(
                ['error' => 'Пользователь с таким email уже зарегистрирован'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = new User();
        $user->assign($authDTO, $this->passwordHasher);

        $rootFolder = Folder::createRootFolder($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($rootFolder);
        $this->entityManager->flush();

        return $this->json(
            [
                'jwt_token' => $this->jwtManager->create($user),
                'user' => $user,
            ]
        );
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] AuthDTO $authDTO
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $authDTO->email]);

        if ($user !== null && $this->passwordHasher->isPasswordValid($user, $authDTO->password)) {
            return $this->json(
                [
                    'jwt_token' => $this->jwtManager->create($user),
                    'user' => $user,
                ]
            );
        }

        return $this->json(['error' => 'Неверный email или пароль'], Response::HTTP_BAD_REQUEST);
    }
}
