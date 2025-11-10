<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Request\CourseRequest;

class CourseService extends BaseService
{
    public function list(User $user): array
    {
        return $this->entityManager->getRepository(Course::class)
            ->findBy(['organization' => $user->getOrganization()]);
    }

    public function getById(User $user, int $id): Course
    {
        $course = $this->entityManager->getRepository(Course::class)
            ->findOneBy(['id' => $id, 'organization' => $user->getOrganization()]);

        if (!$course) throw new NotFoundException('Course not found');
        return $course;
    }

    public function create(User $user, CourseRequest $request): Course
    {
        $course = (new Course())
            ->setTitle($request->title)
            ->setDescription($request->description)
            ->setOrganization($user->getOrganization())
            ->setInstructor($user);

        $this->persistAndFlush($course);
        return $course;
    }

    public function update(User $user, int $id, CourseRequest $request): Course
    {
        $course = $this->getById($user, $id);

        if ($request->title !== null) $course->setTitle($request->title);
        if ($request->description !== null) $course->setDescription($request->description);

        $this->entityManager->flush();
        return $course;
    }

    public function delete(User $user, int $id): void
    {
        $course = $this->getById($user, $id);
        $this->removeAndFlush($course);
    }
}