<?php

namespace App\Data\Enums;

use App\Data\Enums\Permissions as PermissionEnum;

enum Roles: string
{
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    case ADMIN = 'ROLE_ADMIN';
    case INSTRUCTOR = 'ROLE_INSTRUCTOR';
    case STUDENT = 'ROLE_STUDENT';
    case GUEST = 'ROLE_GUEST';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::INSTRUCTOR => 'Instructor',
            self::STUDENT => 'Student',
            self::GUEST => 'Guest',
        };
    }

    public function description(): string
    {
        return $this->label();
    }

    public function defaultPermissions(): array
    {
        return match ($this) {
            self::SUPER_ADMIN => PermissionEnum::values(),
            self::ADMIN => array_merge(
                PermissionEnum::getValuesByResource('organization'),
                PermissionEnum::getValuesByResource('course'),
                PermissionEnum::getValuesByResource('lesson'),
                PermissionEnum::getValuesByResource('enrollment'),
                PermissionEnum::getValuesByResource('user'),
                [PermissionEnum::ROLE_VIEW->value]
            ),
            self::INSTRUCTOR => array_merge(
                PermissionEnum::getValuesByResource('course'),
                PermissionEnum::getValuesByResource('lesson'),
                [
                    PermissionEnum::ENROLLMENT_VIEW->value,
                    PermissionEnum::ENROLLMENT_CREATE->value,
                ]
            ),
            self::STUDENT => [
                PermissionEnum::COURSE_VIEW->value,
                PermissionEnum::LESSON_VIEW->value,
                PermissionEnum::ENROLLMENT_VIEW->value,
            ],
            self::GUEST => [
                PermissionEnum::COURSE_VIEW->value,
            ],
        };
    }
}
