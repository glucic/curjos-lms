<?php
namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[ORM\Table(name: 'lessons')]
class Lesson
{
    #[Groups(['me:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['me:read'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['me:read'])]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Groups(['me:read'])]
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[Groups(['me:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resourceUrl = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getResourceUrl(): ?string
    {
        return $this->resourceUrl;
    }

    public function setResourceUrl(?string $resourceUrl): static
    {
        $this->resourceUrl = $resourceUrl;
        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }
}
