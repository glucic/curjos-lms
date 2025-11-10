<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'Email already exists')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'organization:view'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'organization:view'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Ignore]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'course:view', 'organization:view'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'course:view', 'organization:view'])]
    private ?string $lastName = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'course:view', 'organization:view'])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'course:view', 'organization:view'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create', 'course:view', 'organization:view'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['me:read'])]
    private Organization $organization;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_roles')]
    #[Groups(['me:read', 'user:view', 'user:edit', 'user:create'])]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_map(fn(Role $role) => $role->getName(), $this->roles->toArray());
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoleEntities(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    #[Groups(['me:read'])]
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
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

    public function isSystemUser(): bool
    {
        return $this->organization !== null && $this->organization->isSystemOrganization();
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

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $roleName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getName() === $roleName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->roles as $role) {
            foreach ($role->getPermissions() as $permission) {
                if ($permission->getName() === $permissionName) {
                    return true;
                }
            }
        }

        return false;
    }

    #[Groups(['me:read'])]
    #[SerializedName('permissions')]
    public function getSerializedPermissions(): array
    {
        $permissions = [];
        foreach ($this->getRoleEntities() as $role) {
            foreach ($role->getPermissions() as $permission) {
                $permissions[$permission->getName()] = true;
            }
        }
        return array_keys($permissions);
    }
}
