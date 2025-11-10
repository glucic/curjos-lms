<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Request\LessonRequest;
use App\Service\LessonService;
use App\Data\Enums\Roles;
use App\Data\Enums\Permissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/courses/{courseId}/lessons')]
class LessonController extends BaseController
{
    public function __construct(private LessonService $lessonService) {}

    #[Route('', name: 'api_lessons_list', methods: ['GET'])]
    public function list(int $courseId): JsonResponse
    {
        return $this->handle(function () use ($courseId) {
            $lessons = $this->lessonService->list($courseId);
            return $this->success(
                data: $lessons,
                status: 200,
                context: ['groups' => [Permissions::LESSON_VIEW->value]]
            );
        });
    }

    #[Route('/{lessonId}', name: 'api_lessons_show', methods: ['GET'])]
    public function show(int $courseId, int $lessonId): JsonResponse
    {
        return $this->handle(function () use ($courseId, $lessonId) {
            $lesson = $this->lessonService->get($courseId, $lessonId);
            return $this->success(
                data: $lesson,
                status: 200,
                context: ['groups' => [Permissions::LESSON_VIEW->value]]
            );
        });
    }

    #[Route('', name: 'api_lessons_create', methods: ['POST'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function create(int $courseId, LessonRequest $request): JsonResponse
    {
        return $this->handle(function () use ($courseId, $request) {
            $lesson = $this->lessonService->create($courseId, $request);

            return $this->success(
                data: $lesson,
                status: 201,
                context: ['groups' => [Permissions::LESSON_CREATE->value]]
            );
        });
    }

    #[Route('/{lessonId}', name: 'api_lessons_update', methods: ['PUT', 'PATCH'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function update(int $courseId, int $lessonId, LessonRequest $request): JsonResponse
    {
        return $this->handle(function () use ($courseId, $lessonId, $request) {
            $lesson = $this->lessonService->update($courseId, $lessonId, $request);

            return $this->success(
                data: $lesson,
                status: 200,
                context: ['groups' => [Permissions::LESSON_EDIT->value]]
            );
        });
    }

    #[Route('/{lessonId}', name: 'api_lessons_delete', methods: ['DELETE'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function delete(int $courseId, int $lessonId): JsonResponse
    {
        return $this->handle(function () use ($courseId, $lessonId) {
            $this->lessonService->delete($courseId, $lessonId);
            return $this->success(
                data: null,
                status: 204
            );
        });
    }
}