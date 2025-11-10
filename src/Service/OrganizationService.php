<?php

namespace App\Service;

use App\Entity\Organization;
use App\Exception\NotFoundException;
use App\Repository\OrganizationRepository;
use App\Entity\User;
use App\Request\OrganizationRequest;

class OrganizationService extends BaseService
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private AccessService $access
    ) {
        parent::__construct($this->organizationRepository->getEntityManager());
    }

    /**
     * List Organizations
     */
    public function list(User $user): array
    {
        if ($this->access->isSuperAdmin($user)) {
            return $this->organizationRepository->findNonSystemOrganizations();
        }

        return [$user->getOrganization()];
    }


    /**
     * Fetch Organization by ID
     */
    public function getById(User $user, int $id): Organization
    {
        $organization = $this->organizationRepository->find($id);
        if (!$organization || $organization->isSystemOrganization()) {
            throw new NotFoundException('Organization not found');
        }

        $this->access->assertSameOrganization($user, $organization);
        return $organization;
    }


    /**
     * Create an Organization
     */
    public function create(OrganizationRequest $request): Organization
    {
        $organization = (new Organization())
            ->setName($request->name)
            ->setIsActive($request->isActive);

        $this->persistAndFlush($organization);
        return $organization;
    }

    /**
     * Update an Organization
     */
    public function update(Organization $organization, OrganizationRequest $request): Organization
    {
        $organization->setName($request->name ?? $organization->getName());
        $organization->setIsActive($request->isActive ?? $organization->isActive());

        $this->persistAndFlush($organization);
        return $organization;
    }


    /**
     * Delete an Organization
     */
    public function delete(Organization $organization): void
    {
        $this->removeAndFlush($organization);
    }
}