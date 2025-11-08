<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Traits\ApiResponseTrait;

#[Route('/api/courses')]
class CourseController extends AbstractController
{
    use ApiResponseTrait;

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('', name: 'api_courses_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $courses = $this->entityManager->getRepository(Course::class)->findAll();
        return $this->success(data: $courses, status:200, context: ['groups' => ['me:read']]);
    }

    #[Route('/{id}', name: 'api_courses_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)->find($id);
        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }
        return $this->success(data: $course, status: 200, context: ['groups' => ['me:read']]);
    }

    #[Route('/{id}/lessons', name: 'api_courses_lessons', methods: ['GET'])]
    public function lessons(int $id): JsonResponse
    {
        $course = $this->entityManager->getRepository(Course::class)->find($id);
        if (!$course) {
            return $this->error('Course not found', 'not_found', 404);
        }
        $lessons = $course->getLessons();
        return $this->success(data: $lessons, status: 200, context: ['groups' => ['me:read']]);
    }
}
