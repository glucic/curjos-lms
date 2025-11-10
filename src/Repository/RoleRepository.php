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

    public function findSystemRoles(): array
    {
        return $this->createQueryBuilder('role')
            ->andWhere('role.isSystemRole = :isSystem')
            ->setParameter('isSystem', true)
            ->orderBy('role.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByName(string $name): ?Role
    {
        return $this->createQueryBuilder('r')
            ->where('r.name = :name')
            ->andWhere('r.name != :superAdmin')
            ->setParameter('name', $name)
            ->setParameter('superAdmin', Roles::SUPER_ADMIN->value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
