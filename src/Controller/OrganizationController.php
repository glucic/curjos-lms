<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Enum\Roles;
use App\Enum\Permissions;
use App\Request\OrganizationRequest;
use App\Traits\ApiResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/organizations')]
class OrganizationController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('', name: 'api_organizations_list', methods: ['GET'])]
    #[IsGranted(Roles::SUPER_ADMIN->value)]
    public function list(): JsonResponse
    {
        $organizations = $this->entityManager
            ->getRepository(Organization::class)
            ->findAll();

        $organizations = array_values(
            array_filter($organizations, fn($org) => !$org->isSystemOrganization())
        );

        return $this->success(
            $organizations,
            200,
            context: ['groups' => [Permissions::ORGANIZATION_VIEW->value]]
        );
    }

    #[Route('/{id}', name: 'api_organizations_show', methods: ['GET'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function show(int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $organization = $this->entityManager->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        if ($organization->isSystemOrganization()) {
            return $this->error('Cannot access a system organization', 'forbidden', 403);
        }

        if (!in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true) &&
            $organization !== $currentUser->getOrganization()
        ) {
            return $this->error('Access denied', 'forbidden', 403);
        }
        
        return $this->success($organization, 200, context: ['groups' => [Permissions::ORGANIZATION_VIEW->value]]);
    }

    #[Route('', name: 'api_organizations_create', methods: ['POST'])]
    #[IsGranted(Roles::SUPER_ADMIN->value)]
    public function create(OrganizationRequest $request): JsonResponse
    {
        $organization = new Organization();
        $organization->setName($request->name)
                     ->setIsActive($request->isActive ?? true);

        $this->entityManager->persist($organization);
        $this->entityManager->flush();

        return $this->success($organization, 201, context:['groups' => [Permissions::ORGANIZATION_CREATE->value]]);
    }

    #[Route('/{id}', name: 'api_organizations_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function update(int $id, OrganizationRequest $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $organization = $this->entityManager->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        if ($organization->isSystemOrganization()) {
            return $this->error('Cannot access a system organization', 'forbidden', 403);
        }

        if (!in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true) &&
            $organization !== $currentUser->getOrganization()
        ) {
            return $this->error('Access denied', 'forbidden', 403);
        }

        $organization->setName($request->name ?? $organization->getName());
        if (isset($request->isActive)) {
            $organization->setIsActive($request->isActive);
        }

        $this->entityManager->flush();

        return $this->success($organization, 200, context:['groups' => [Permissions::ORGANIZATION_EDIT->value]]);
    }

    #[Route('/{id}', name: 'api_organizations_delete', methods: ['DELETE'])]
    #[IsGranted(Roles::SUPER_ADMIN->value)]
    public function delete(int $id): JsonResponse
    {
        $organization = $this->entityManager->getRepository(Organization::class)->find($id);

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        if ($organization->isSystemOrganization()) {
            return $this->error('Cannot delete a system organization', 'forbidden', 403);
        }

        $this->entityManager->remove($organization);
        $this->entityManager->flush();

        return $this->success('Organization deleted successfully', 204);
    }
}
