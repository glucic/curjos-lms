<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function findOneByName(string $name): ?Permission
    {
        return $this->createQueryBuilder('permission')
            ->andWhere('permission.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByResource(string $resource): array
    {
        return $this->createQueryBuilder('permission')
            ->andWhere('permission.resource = :resource')
            ->setParameter('resource', $resource)
            ->orderBy('permission.action', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllGroupedByResource(): array
    {
        $permissions = $this->createQueryBuilder('permission')
            ->orderBy('permission.resource', 'ASC')
            ->addOrderBy('permission.action', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($permissions as $permission) {
            $resource = $permission->getResource();
            if (!isset($grouped[$resource])) {
                $grouped[$resource] = [];
            }
            $grouped[$resource][] = $permission;
        }

        return $grouped;
    }
}
