<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Data\Enums\Permissions;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: 'courses')]
#[ORM\HasLifecycleCallbacks]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([Permissions::COURSE_CREATE->value, Permissions::COURSE_VIEW->value])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    #[Groups([Permissions::COURSE_CREATE->value, Permissions::COURSE_VIEW->value])]
    private ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000)]
    #[Groups([Permissions::COURSE_CREATE->value, Permissions::COURSE_VIEW->value])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Permissions::COURSE_CREATE->value, Permissions::COURSE_VIEW->value])]
    private ?Organization $organization = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups([Permissions::COURSE_CREATE->value])]
    private ?User $instructor = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Lesson::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups([Permissions::LESSON_VIEW->value])]
    private Collection $lessons;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups([Permissions::COURSE_VIEW->value])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups([Permissions::COURSE_VIEW->value])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    #[Groups([Permissions::COURSE_CREATE->value])]
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups([Permissions::COURSE_CREATE->value])]
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getInstructor(): ?User
    {
        return $this->instructor;
    }

    public function setInstructor(?User $instructor): self
    {
        $this->instructor = $instructor;
        return $this;
    }

    #[Groups(['course:view', 'lesson:view'])]
    public function getInstructorName(): ?string
    {
        if ($this->instructor === null) {
            return null;
        }

        return $this->instructor->getFullName();
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    #[Groups(['course:view', 'lesson:view'])]
    public function getLessonsCount(): int
    {
        return $this->lessons->count() ?? 0;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCourse($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            if ($lesson->getCourse() === $this) {
                $lesson->setCourse(null);
            }
        }

        return $this;
    }

    #[Groups(['course:view'])]
    public function getDifficulty(): int
    {
        $difficulties = array_map(
            fn(Lesson $lesson) => $lesson->getDifficulty(),
            $this->lessons->toArray()
        );

        return !empty($difficulties) ? max($difficulties) : 0;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
