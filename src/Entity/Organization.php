<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table(name: 'organizations')]
#[UniqueEntity(fields: ['slug'], message: 'Organization slug already exists')]
#[ORM\HasLifecycleCallbacks]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['me:read', 'course:view', 'organization:view', 'organization:edit'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['me:read', 'course:view', 'organization:view', 'organization:edit'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['me:read', 'organization:view', 'organization:edit'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['organization:view', 'organization:edit'])]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['organization:view', 'organization:edit'])]
    private bool $isSystemOrganization = false;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['organization:view', 'organization:edit'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['organization:view', 'organization:edit'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: User::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['organization:view'])]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'organization', targetEntity: Course::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $courses;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function generateSlug(): void
    {
        if ($this->slug === null && $this->name !== null) {
            $slugger = new AsciiSlugger();
            $this->slug = $slugger->slug($this->name)->lower()->toString();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isSystemOrganization(): bool
    {
        return $this->isSystemOrganization;
    }

    public function setIsSystemOrganization(bool $isSystemOrganization): static
    {
        $this->isSystemOrganization = $isSystemOrganization;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setOrganization($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            if ($user->getOrganization() === $this) {
                $user->setOrganization(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setOrganization($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            if ($course->getOrganization() === $this) {
                $course->setOrganization(null);
            }
        }

        return $this;
    }
}
