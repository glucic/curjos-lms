<?php

namespace App\Service;

use App\Entity\User;
use App\Data\Enums\Roles;

class AccessService
{
    public function isSuperAdmin(User $user): bool
    {
        return in_array(Roles::SUPER_ADMIN->value, $user->getRoles(), true);
    }

    public function assertSameOrganization(User $actor, object $target): void
    {
        if (!$this->isSuperAdmin($actor) && method_exists($target, 'getOrganization')) {
            if ($actor->getOrganization() !== $target->getOrganization()) {
                throw new \RuntimeException('Access forbidden: organization mismatch');
            }
        }
    }
}