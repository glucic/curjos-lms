<?php

namespace App\Request;

use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CourseRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    public string $title;

    #[Assert\Length(max: 2000)]
    public ?string $description = null;
}