<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Organization;
use App\Request\UserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService extends BaseService
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private AccessService $access,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($entityManager);
    }

    public function getAllUsers(User $currentUser): array
    {
        return $this->access->isSuperAdmin($currentUser)
            ? $this->entityManager->getRepository(User::class)->findAll()
            : $this->entityManager->getRepository(User::class)
                ->findBy(['organization' => $currentUser->getOrganization()]);
    }

    public function getUserById(int $id, User $currentUser): User
    {
        $user = $this->findEntityOrFail(User::class, $id);
        $this->access->assertSameOrganization($currentUser, $user);
        return $user;
    }

    public function createUser(UserRequest $data, User $actor): User
    {
        $user = (new User())
            ->setFirstName($data->firstName)
            ->setLastName($data->lastName)
            ->setEmail($data->email)
            ->setPassword($this->passwordHasher->hashPassword($actor, $data->password));

        $org = $this->access->isSuperAdmin($actor)
            ? $this->entityManager->getRepository(Organization::class)->find($data->organizationId)
            : $actor->getOrganization();

        $user->setOrganization($org);

        if ($data->role) {
            $role = $this->entityManager
                ->getRepository(Role::class)
                ->findByName($data->role->getName());

            if (!$role) {
                throw new \InvalidArgumentException('Invalid role selected');
            }

            $user->setRole($role);
        }

        $this->persistAndFlush($user);

        return $user;
    }

    public function updateUser(int $id, UserRequest $data, User $actor): User
    {
        $user = $this->getUserById($id, $actor);

        $user->setFirstName($data->firstName ?? $user->getFirstName());
        $user->setLastName($data->lastName ?? $user->getLastName());
        $user->setEmail($data->email ?? $user->getEmail());

        if ($data->password) {
            $user->setPassword($this->passwordHasher->hashPassword($actor, $data->password));
        }

        if ($data->role) {
            $role = $this->entityManager
                ->getRepository(Role::class)
                ->findByName($data->role->getName());

            if (!$role) {
                throw new \InvalidArgumentException('Invalid role selected');
            }

            $user->setRole($role);
        }

        $this->persistAndFlush($user);

        return $user;
    }

    public function deleteUser(int $id, User $actor): void
    {
        $user = $this->getUserById($id, $actor);
        $this->removeAndFlush($user);
    }
}