<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        return $this->createQueryBuilder('r')
            ->andWhere('r.organization = :organizationId')
            ->setParameter('organizationId', $organizationId)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findSystemRoles(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.isSystemRole = :isSystem')
            ->setParameter('isSystem', true)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByNameAndOrganization(string $name, int $organizationId): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.name = :name')
            ->andWhere('r.organization = :organizationId')
            ->setParameter('name', $name)
            ->setParameter('organizationId', $organizationId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
