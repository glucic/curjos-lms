<?php

namespace App\Service;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Exception\NotFoundException;
use App\Request\LessonRequest;

class LessonService extends BaseService
{
    public function list(int $courseId): array
    {
        $course = $this->getCourse($courseId);
        return $this->entityManager->getRepository(Lesson::class)
            ->findBy(['course' => $course]);
    }

    public function get(int $courseId, int $lessonId): Lesson
    {
        $lesson = $this->entityManager->getRepository(Lesson::class)
            ->findOneBy([
                'id' => $lessonId,
                'course' => $this->getCourse($courseId),
            ]);

        if (!$lesson) throw new NotFoundException('Lesson not found');
        return $lesson;
    }

    public function create(int $courseId, LessonRequest $request): Lesson
    {
        $course = $this->getCourse($courseId);

        $lesson = (new Lesson())
            ->setTitle($request->title)
            ->setDescription($request->description)
            ->setDifficulty($request->difficulty)
            ->setType($request->type)
            ->setResourceUrl($request->resourceUrl)
            ->setCourse($course);

        $this->persistAndFlush($lesson);
        return $lesson;
    }

    public function update(int $courseId, int $lessonId, LessonRequest $request): Lesson
    {
        $lesson = $this->get($courseId, $lessonId);

        $lesson->setTitle($request->title ?? $lesson->getTitle());
        $lesson->setDescription($request->description ?? $lesson->getDescription());
        $lesson->setDifficulty($request->difficulty ?? $lesson->getDifficulty());
        $lesson->setType($request->type ?? $lesson->getType());
        $lesson->setResourceUrl($request->resourceUrl ?? $lesson->getResourceUrl());

        $this->persistAndFlush($lesson);
        return $lesson;
    }

    public function delete(int $courseId, int $lessonId): void
    {
        $lesson = $this->get($courseId, $lessonId);
        $this->removeAndFlush($lesson);
    }

    private function getCourse(int $courseId): Course
    {
        $course = $this->entityManager->getRepository(Course::class)->find($courseId);
        if (!$course) throw new NotFoundException('Course not found');
        return $course;
    }
}