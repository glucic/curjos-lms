<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Request\CourseRequest;
use App\Service\CourseService;
use App\Data\Enums\Roles;
use App\Data\Enums\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/courses')]
class CourseController extends BaseController
{
    public function __construct(private CourseService $courseService) {}

    #[Route('', name: 'api_courses_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->handle(function () {
            $courses = $this->courseService->list($this->getCurrentUser());
            return $this->success(
                data: $courses,
                status: 200,
                context: ['groups' => [Permissions::COURSE_VIEW->value]]
            );
        });
    }

    #[Route('/{id}', name: 'api_courses_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        return $this->handle(function () use ($id) {
            $course = $this->courseService->getById($this->getCurrentUser(), $id);
            return $this->success(
                data: $course,
                status: 200,
                context: ['groups' => [Permissions::COURSE_VIEW->value, Permissions::LESSON_VIEW->value]]
            );
        });
    }

    #[Route('', name: 'api_courses_create', methods: ['POST'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function create(CourseRequest $request): JsonResponse
    {
        return $this->handle(function () use ($request) {
            $course = $this->courseService->create(
                $this->getCurrentUser(),
                $request
            );
            return $this->success(
                data: $course,
                status: 201,
                context: ['groups' => [Permissions::COURSE_CREATE->value]]
            );
        });
    }

    #[Route('/{id}', name: 'api_courses_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function update(int $id, CourseRequest $request): JsonResponse
    {
        return $this->handle(function () use ($id, $request) {
            $course = $this->courseService->update(
                $this->getCurrentUser(),
                $id,
                $request
            );
            return $this->success(
                data: $course,
                status: 200,
                context: ['groups' => [Permissions::COURSE_EDIT->value]]
            );
        });
    }

    #[Route('/{id}', name: 'api_courses_delete', methods: ['DELETE'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function delete(int $id): JsonResponse
    {
        return $this->handle(function () use ($id) {
            $this->courseService->delete($this->getCurrentUser(), $id);
            return $this->success(
                data: 'Course deleted successfully',
                status: 204
            );
        });
    }
}