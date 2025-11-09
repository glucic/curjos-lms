<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Enum\Roles;
use App\Enum\Permissions;
use App\Request\LessonRequest;
use App\Traits\ApiResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/courses/{courseId}/lessons')]
class LessonController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('', name: 'api_lessons_list', methods: ['GET'])]
    public function list(int $courseId): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)
            ->find($courseId);

        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $lessons = $this->entityManager->getRepository(Lesson::class)
            ->findBy(['course' => $course]);

        return $this->success(data: $lessons, status: 200, context: ['groups' => [Permissions::LESSON_VIEW->value]]);
    }

    #[Route('/{lessonId}', name: 'api_lessons_show', methods: ['GET'])]
    public function show(int $courseId, int $lessonId): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)
            ->find($courseId);

        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $lesson = $this->entityManager->getRepository(Lesson::class)
            ->findOneBy(['id' => $lessonId, 'course' => $course]);

        if (!$lesson) {
            return $this->error('Lesson not found', 'not_found', 404);
        }

        return $this->success(data: $lesson, status: 200, context: ['groups' => [Permissions::LESSON_VIEW->value]]);
    }

    #[Route('', name: 'api_lessons_create', methods: ['POST'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function create(int $courseId, LessonRequest $request): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)
            ->find($courseId);

        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $lesson = new Lesson();
        $lesson->setTitle($request->title);
        $lesson->setDescription($request->description);
        $lesson->setDifficulty($request->difficulty ?? 0);
        $lesson->setType($request->type);
        $lesson->setResourceUrl($request->resourceUrl);
        $lesson->setCourse($course);

        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        return $this->success(
            data: $lesson,
            status: 201,
            context: ['groups' => [Permissions::LESSON_CREATE->value]]
        );
    }

    #[Route('/{lessonId}', name: 'api_lessons_update', methods: ['PUT'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function update(int $courseId, int $lessonId, LessonRequest $request): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)->find($courseId);
        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $lesson = $this->entityManager->getRepository(Lesson::class)
            ->findOneBy(['id' => $lessonId, 'course' => $course]);

        if (!$lesson) {
            return $this->error('Lesson not found', 'not_found', 404);
        }

        $lesson->setTitle($request->title ?? $lesson->getTitle());
        $lesson->setDescription($request->description ?? $lesson->getDescription());
        $lesson->setDifficulty($request->difficulty ?? $lesson->getDifficulty());
        $lesson->setType($request->type ?? $lesson->getType());
        $lesson->setResourceUrl($request->resourceUrl ?? $lesson->getResourceUrl());

        $this->entityManager->flush();

        return $this->success(
            data: $lesson,
            status: 200,
            context: ['groups' => [Permissions::LESSON_EDIT->value]]
        );
    }

    #[Route('/{lessonId}', name: 'api_lessons_delete', methods: ['DELETE'])]
    #[IsGranted(Roles::INSTRUCTOR->value)]
    public function delete(int $courseId, int $lessonId): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)->find($courseId);
        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }

        $lesson = $this->entityManager->getRepository(Lesson::class)
            ->findOneBy(['id' => $lessonId, 'course' => $course]);

        if (!$lesson) {
            return $this->error('Lesson not found', 'not_found', 404);
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        return $this->success(
            data: null,
            status: 204
        );
    }

}
