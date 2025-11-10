<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class OrganizationRequest
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\Type('bool')]
    public ?bool $isActive = true;
}
