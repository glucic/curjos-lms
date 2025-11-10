<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use App\Data\Enums\Roles;

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

    #[Assert\NotBlank]
    public string $role = Roles::STUDENT->value;

    public ?int $organizationId = null;
}
