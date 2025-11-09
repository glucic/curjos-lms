<?php

namespace App\Controller;

use App\Entity\Course;
use App\Enum\Roles;
use App\Enum\Permissions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Traits\ApiResponseTrait;
use App\Request\CourseRequest;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/courses')]
class CourseController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('', name: 'api_courses_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        $organization = method_exists($user, 'getOrganization') ? $user->getOrganization() : null;

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        $courses = $this->entityManager->getRepository(Course::class)
            ->findByOrganization($organization);

        return $this->success(data: $courses, status: 200, context: ['groups' => [Permissions::COURSE_VIEW->value]]);
    }

    #[Route('/{id}', name: 'api_courses_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->getUser();
        $organization = method_exists($user, 'getOrganization') ? $user->getOrganization() : null;

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        $course = $this->entityManager->getRepository(Course::class)
            ->findOneByIdAndOrganization($id, $organization);

        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        return $this->success(data: $course, status: 200, context: ['groups' => [Permissions::COURSE_VIEW->value, Permissions::LESSON_VIEW->value]]);
    }

    #[Route('', name: 'api_courses_create', methods: ['POST'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function create(CourseRequest $request): JsonResponse
    {
        $user = $this->getUser();
        $organization = method_exists($user, 'getOrganization') ? $user->getOrganization() : null;

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        $course = new Course();
        $course->setTitle($request->title);
        $course->setDescription($request->description);
        $course->setOrganization($organization);
        $course->setInstructor($user);

        $this->entityManager->persist($course);
        $this->entityManager->flush();

        return $this->success(
            data: $course,
            status: 201,
            context: ['groups' => [Permissions::COURSE_CREATE->value]]
        );
    }

    #[Route('/{id}', name: 'api_courses_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function update(int $id, CourseRequest $request): JsonResponse
    {
        $user = $this->getUser();
        $organization = method_exists($user, 'getOrganization') ? $user->getOrganization() : null;

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        $course = $this->entityManager->getRepository(Course::class)
            ->findOneByIdAndOrganization($id, $organization);

        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $course->setTitle($request->title ?? $course->getTitle());
        $course->setDescription($request->description ?? $course->getDescription());

        $this->entityManager->flush();

        return $this->success(
            data: $course,
            status: 200,
            context: ['groups' => [Permissions::COURSE_EDIT->value]]
        );
    }

    #[Route('/{id}', name: 'api_courses_delete', methods: ['DELETE'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function delete(int $id): JsonResponse
    {
        $user = $this->getUser();
        $organization = method_exists($user, 'getOrganization') ? $user->getOrganization() : null;

        if (!$organization) {
            return $this->error('Organization not found', 'not_found', 404);
        }

        $course = $this->entityManager->getRepository(Course::class)
            ->findOneByIdAndOrganization($id, $organization);

        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $this->entityManager->remove($course);
        $this->entityManager->flush();

        return $this->success('Course deleted successfully', status: 204);
    }

}
