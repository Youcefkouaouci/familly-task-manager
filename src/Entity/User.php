<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\OneToMany(mappedBy: 'child', targetEntity: TaskAssignment::class, cascade: ['persist', 'remove'])]
    private Collection $assignedTasks;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Task::class)]
    private Collection $createdTasks;

    public function __construct()
    {
        $this->assignedTasks = new ArrayCollection();
        $this->createdTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function isParent(): bool
    {
        return in_array('ROLE_PARENT', $this->roles);
    }

    public function isChild(): bool
    {
        return in_array('ROLE_CHILD', $this->roles);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, TaskAssignment>
     */
    public function getAssignedTasks(): Collection
    {
        return $this->assignedTasks;
    }

    public function addAssignedTask(TaskAssignment $taskAssignment): self
    {
        if (!$this->assignedTasks->contains($taskAssignment)) {
            $this->assignedTasks->add($taskAssignment);
            $taskAssignment->setChild($this);
        }

        return $this;
    }

    public function removeAssignedTask(TaskAssignment $taskAssignment): self
    {
        if ($this->assignedTasks->removeElement($taskAssignment)) {
            // set the owning side to null (unless already changed)
            if ($taskAssignment->getChild() === $this) {
                $taskAssignment->setChild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getCreatedTasks(): Collection
    {
        return $this->createdTasks;
    }

    public function addCreatedTask(Task $task): self
    {
        if (!$this->createdTasks->contains($task)) {
            $this->createdTasks->add($task);
            $task->setParent($this);
        }

        return $this;
    }

    public function removeCreatedTask(Task $task): self
    {
        if ($this->createdTasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getParent() === $this) {
                $task->setParent(null);
            }
        }

        return $this;
    }
}