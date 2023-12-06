<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'users', methods: ['GET'])]
    public function list(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();

        return new JsonResponse(
            $serializer->serialize($users, 'json', ['groups' => ['getUsers']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/users/{id}', name: 'user_get', methods: ['GET'])]
    public function get(User $user, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($user, 'json', ['groups' => ['getUsers']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
