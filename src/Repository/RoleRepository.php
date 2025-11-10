<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Data\Enums\Roles;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function findByOrganization(int $organizationId): array
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.organization = :organizationId')
            ->setParameter('organizationId', $organizationId)
            ->orderBy('role.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findSystemRoles(): array
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.isSystemRole = :isSystem')
            ->setParameter('isSystem', true)
            ->orderBy('role.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByNameAndOrganization(string $name, int $organizationId): ?Role
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.name = :name')
            ->andWhere('role.organization = :organizationId')
            ->andWhere('role.name != :superAdmin')
            ->setParameter('name', $name)
            ->setParameter('organizationId', $organizationId)
            ->setParameter('superAdmin', Roles::SUPER_ADMIN->value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
