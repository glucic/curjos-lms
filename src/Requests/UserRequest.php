<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UserRequest
{
    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank(groups: ['create'])]
    public ?string $password = null;

    #[Assert\Type('array')]
    public ?array $roles = [];

    public ?int $organizationId = null;
}
