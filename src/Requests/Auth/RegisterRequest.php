<?php

namespace App\Request\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 64)]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50)]
    public string $lastName;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    public string $organization;
}
