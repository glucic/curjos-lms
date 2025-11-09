<?php
namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use function Symfony\Component\Clock\now;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[ORM\Table(name: 'lessons')]
#[ORM\HasLifecycleCallbacks]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['course:view','lesson:view'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['lesson:view'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['lesson:view'])]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\Range(min: 0, max: 5, notInRangeMessage: 'Difficulty must be between {{ min }} and {{ max }}.')]
    #[Groups(['course:view','lesson:view'])]
    private int $difficulty = 0;

    #[ORM\Column(length: 50)]
    #[Groups(['lesson:view'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['lesson:view'])]
    private ?string $resourceUrl = null;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['lesson:view'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['lesson:view'])]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;
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
