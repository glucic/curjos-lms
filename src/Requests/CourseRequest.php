<?php

namespace App\Request;

use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CourseRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    private string $title;

    #[Assert\Length(max: 2000)]
    private ?string $description = null;
}