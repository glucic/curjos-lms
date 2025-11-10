<?php

namespace App\Request;

use App\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class LessonRequest extends BaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    public string $title;

    #[Assert\Length(max: 2000)]
    public ?string $description = null;

    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: 'Difficulty must be between {{ min }} and {{ max }}.'
    )]
    public ?int $difficulty = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $type;

    #[Assert\Url]
    #[Assert\Length(max: 255)]
    public ?string $resourceUrl = null;
}
