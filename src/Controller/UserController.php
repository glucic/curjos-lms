<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Enum\Roles;
use App\Enum\Permissions;
use App\Request\UserRequest;
use App\Traits\ApiResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('', name: 'api_users_list', methods: ['GET'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function list(): JsonResponse
    {
        $currentUser = $this->getUser();
        $isSuperAdmin = in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true);

        $users = $isSuperAdmin
            ? $this->entityManager->getRepository(User::class)->findAll()
            : $this->entityManager->getRepository(User::class)->findBy(['organization' => $currentUser->getOrganization()]);

        return $this->success($users, 200, context: ['groups' => [Permissions::USER_VIEW->value]]);
    }

    #[Route('/{id}', name: 'api_users_show', methods: ['GET'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function show(int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->error('User not found', 'not_found', 404);
        }

        if (!in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true) &&
            $user->getOrganization() !== $currentUser->getOrganization()
        ) {
            return $this->error('Access denied', 'forbidden', 403);
        }

        return $this->success($user, 200, context: ['groups' => [Permissions::USER_VIEW->value]]);
    }

    #[Route('', name: 'api_users_create', methods: ['POST'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function create(UserRequest $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $user = new User();
        $user->setFirstName($request->firstName)
             ->setLastName($request->lastName)
             ->setEmail($request->email)
             ->setPassword($request->password); // hash in setter

        $organization = in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true)
            ? $this->entityManager->getRepository('App\Entity\Organization')->find($request->organizationId)
            : $currentUser->getOrganization();
        $user->setOrganization($organization);

        if (!empty($request->roles)) {
            foreach ($request->roles as $roleName) {
                $role = $this->entityManager->getRepository(Role::class)
                    ->findOneBy(['name' => $roleName, 'organization' => $organization]);
                if ($role) {
                    $user->addRole($role);
                }
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->success($user, 201, context: ['groups' => [Permissions::USER_CREATE->value]]);
    }

    #[Route('/{id}', name: 'api_users_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function update(int $id, UserRequest $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->error('User not found', 'not_found', 404);
        }

        if (!in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true) &&
            $user->getOrganization() !== $currentUser->getOrganization()
        ) {
            return $this->error('Access denied', 'forbidden', 403);
        }

        $user->setFirstName($request->firstName ?? $user->getFirstName());
        $user->setLastName($request->lastName ?? $user->getLastName());
        $user->setEmail($request->email ?? $user->getEmail());
        if ($request->password) {
            $user->setPassword($request->password); // hash in setter
        }

        // Update roles
        if (!empty($request->roles)) {
            foreach ($user->getRoleEntities() as $existingRole) {
                $user->removeRole($existingRole);
            }
            foreach ($request->roles as $roleName) {
                $role = $this->entityManager->getRepository(Role::class)
                    ->findOneBy(['name' => $roleName, 'organization' => $user->getOrganization()]);
                if ($role) {
                    $user->addRole($role);
                }
            }
        }

        $this->entityManager->flush();

        return $this->success($user, 200, ['groups' => [Permissions::USER_EDIT->value]]);
    }

    #[Route('/{id}', name: 'api_users_delete', methods: ['DELETE'])]
    #[IsGranted(Roles::ADMIN->value)]
    public function delete(int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->error('User not found', 'not_found', 404);
        }

        if (!in_array(Roles::SUPER_ADMIN->value, $currentUser->getRoles(), true) &&
            $user->getOrganization() !== $currentUser->getOrganization()
        ) {
            return $this->error('Access denied', 'forbidden', 403);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->success(null, 204);
    }
}
