<?php

namespace App\Data\Enums;

enum Permissions: string
{
    case ORGANIZATION_VIEW = 'organization:view';
    case ORGANIZATION_CREATE = 'organization:create';
    case ORGANIZATION_EDIT = 'organization:edit';
    case ORGANIZATION_DELETE = 'organization:delete';
    case COURSE_VIEW = 'course:view';
    case COURSE_CREATE = 'course:create';
    case COURSE_EDIT = 'course:edit';
    case COURSE_DELETE = 'course:delete';
    case COURSE_PUBLISH = 'course:publish';
    case LESSON_VIEW = 'lesson:view';
    case LESSON_CREATE = 'lesson:create';
    case LESSON_EDIT = 'lesson:edit';
    case LESSON_DELETE = 'lesson:delete';
    case ENROLLMENT_VIEW = 'enrollment:view';
    case ENROLLMENT_CREATE = 'enrollment:create';
    case ENROLLMENT_MANAGE = 'enrollment:manage';
    case USER_VIEW = 'user:view';
    case USER_CREATE = 'user:create';
    case USER_EDIT = 'user:edit';
    case USER_DELETE = 'user:delete';
    case ROLE_VIEW = 'role:view';
    case ROLE_MANAGE = 'role:manage';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function resource(): string
    {
        return explode(':', $this->value)[0];
    }

    public function action(): string
    {
        return explode(':', $this->value)[1];
    }

    public static function getByResource(string $resource): array
    {
        return array_filter(self::cases(), fn(self $p) => $p->resource() === $resource);
    }

    public static function getValuesByResource(string $resource): array
    {
        return array_map(fn(self $p) => $p->value, self::getByResource($resource));
    }

    public function label(): string
    {
        return match ($this) {
            self::ORGANIZATION_VIEW => 'View Organizations',
            self::ORGANIZATION_CREATE => 'Create Organizations',
            self::ORGANIZATION_EDIT => 'Edit Organizations',
            self::ORGANIZATION_DELETE => 'Delete Organizations',
            self::COURSE_VIEW => 'View Courses',
            self::COURSE_CREATE => 'Create Courses',
            self::COURSE_EDIT => 'Edit Courses',
            self::COURSE_DELETE => 'Delete Courses',
            self::COURSE_PUBLISH => 'Publish Courses',
            self::LESSON_VIEW => 'View Lessons',
            self::LESSON_CREATE => 'Create Lessons',
            self::LESSON_EDIT => 'Edit Lessons',
            self::LESSON_DELETE => 'Delete Lessons',
            self::ENROLLMENT_VIEW => 'View Enrollments',
            self::ENROLLMENT_CREATE => 'Create Enrollments',
            self::ENROLLMENT_MANAGE => 'Manage Enrollments',
            self::USER_VIEW => 'View Users',
            self::USER_CREATE => 'Create Users',
            self::USER_EDIT => 'Edit Users',
            self::USER_DELETE => 'Delete Users',
            self::ROLE_VIEW => 'View Roles',
            self::ROLE_MANAGE => 'Manage Roles',
        };
    }

    public function description(): string
    {
        return $this->label();
    }

    public static function groupedByResource(): array
    {
        $grouped = [];
        foreach (self::cases() as $perm) {
            $grouped[$perm->resource()][] = $perm->value;
        }
        return $grouped;
    }
}
