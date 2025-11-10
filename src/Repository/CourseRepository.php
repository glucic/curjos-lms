<?php
namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Organization;

class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findByOrganization(Organization $organization): array
    {
        return $this->createQueryBuilder('course')
            ->where('course.organization = :organization')
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getResult();
    }

    public function findOneByIdAndOrganization(int $id, Organization $organization): ?Course
    {
        return $this->createQueryBuilder('course')
            ->where('course.id = :id')
            ->andWhere('course.organization = :organization')
            ->setParameter('id', $id)
            ->setParameter('organization', $organization)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
