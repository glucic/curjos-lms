<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Data\Enums\Roles;
use App\Data\Enums\Permissions;
use App\Request\UserRequest;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserController extends BaseController
{
    public function __construct(private UserService $userService) {}

    #[Route('', methods: ['GET'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function list(): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->userService->getAllUsers($this->getCurrentUser()),
            200,
            context: ['groups' => [Permissions::USER_VIEW->value]]
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function show(int $id): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->userService->getUserById($id, $this->getCurrentUser()),
            200,
            context: ['groups' => [Permissions::USER_VIEW->value]]
        ));
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function create(UserRequest $request): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->userService->createUser($request, $this->getCurrentUser()),
            201,
            context: ['groups' => [Permissions::USER_CREATE->value]]
        ));
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function update(int $id, UserRequest $request): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->userService->updateUser($id, $request, $this->getCurrentUser()),
            200,
            context: ['groups' => [Permissions::USER_EDIT->value]]
        ));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function delete(int $id): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->userService->deleteUser($id, $this->getCurrentUser()),
            204
        ));
    }
}