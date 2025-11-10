<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Role;

class UserRequest
{
    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    public ?string $password = null;

    #[Assert\NotNull]
    public ?Role $role = null;

    public ?int $organizationId = null;
}
