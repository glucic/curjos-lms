<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Request\Auth\RegisterRequest;
use App\Traits\ApiResponseTrait;

#[Route('/api/auth')]
final class AuthController extends AbstractController
{
    use ApiResponseTrait;
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    #[Route('/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(): never
    {
        throw new \LogicException('This method will be intercepted by the security layer.');
    }

   #[Route('/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(RegisterRequest $request): JsonResponse
    {
        $organization = $this->entityManager
            ->getRepository(Organization::class)
            ->findOneBy(['slug' => $request->organization]);

        if ($organization->isSystemOrganization()) {
            return $this->json([
                'error' => 'invalid_organization',
                'message' => 'Cannot register under the system organization.'
            ], Response::HTTP_FORBIDDEN);
        }

        $studentRole = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy([
                'name' => 'ROLE_STUDENT',
                'organization' => $organization,
            ]);

        if (!$studentRole) {
            return $this->json([
                'error' => 'role_not_found',
                'message' => 'Student role is not configured for this organization.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = new User();
        $user->setEmail($request->email)
            ->setFirstName($request->firstName)
            ->setLastName($request->lastName)
            ->setOrganization($organization)
            ->setIsActive(true);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
        $user->setPassword($hashedPassword);
        $user->addRole($studentRole);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->success([
            'message' => 'Registration successful',
            'user' => $user->getEmail(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
       $user = $this->getUser();

        if (!$user) {
            return $this->error('User not authenticated', 'not_authenticated', Response::HTTP_UNAUTHORIZED);
        }
        
        return $this->json($user, Response::HTTP_OK, [], ['groups' => ['me:read']]);
    }
}