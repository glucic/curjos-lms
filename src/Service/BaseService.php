<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Exception\NotFoundException;

abstract class BaseService
{
    public function __construct(protected EntityManagerInterface $entityManager) {}

    protected function findEntityOrFail(string $entityClass, int $id): object
    {
        $entity = $this->entityManager->getRepository($entityClass)->find($id);

        if (!$entity) {
            throw new NotFoundException("$entityClass with ID $id not found");
        }

        return $entity;
    }

    protected function persistAndFlush(object $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    protected function removeAndFlush(object $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}