<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Data\Enums\Roles;
use App\Data\Enums\Permissions;
use App\Request\OrganizationRequest;
use App\Service\OrganizationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/organizations')]
class OrganizationController extends BaseController
{
    public function __construct(private OrganizationService $organizationService) {}

    #[Route('', methods: ['GET'])]
    #[IsGranted(Roles::SUPER_ADMIN->value)]
    public function list(): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->organizationService->list($this->getCurrentUser()),
            200,
            context: ['groups' => [Permissions::ORGANIZATION_VIEW->value]]
        ));
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function show(int $id): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->organizationService->getById($this->getCurrentUser(), $id),
            200,
            context: ['groups' => [Permissions::ORGANIZATION_VIEW->value]]
        ));
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted(Roles::SUPER_ADMIN->value)]
    public function create(OrganizationRequest $request): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->organizationService->create($request),
            201,
            context: ['groups' => [Permissions::ORGANIZATION_CREATE->value]]
        ));
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function update(int $id, OrganizationRequest $request): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->organizationService->update(
                $this->organizationService->getById($this->getCurrentUser(), $id),
                $request
            ),
            200,
            context: ['groups' => [Permissions::ORGANIZATION_EDIT->value]]
        ));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted(Roles::SUPER_ADMIN->value)]
    public function delete(int $id): JsonResponse
    {
        return $this->handle(fn() => $this->success(
            $this->organizationService->delete(
                $this->organizationService->getById($this->getCurrentUser(), $id)
            ),
            204
        ));
    }
}