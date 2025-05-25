<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $category = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $recompense = null;

    /**
     * @var Collection<int, TaskAssignment>
     */
    #[ORM\OneToMany(targetEntity: TaskAssignment::class, mappedBy: 'Task')]
    private Collection $taskAssignments;

    #[ORM\ManyToOne(inversedBy: 'createdTasks')]
    private ?User $user = null;

    public function __construct()
    {
        $this->taskAssignments = new ArrayCollection();
    }

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

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

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

    public function getRecompense(): ?string
    {
        return $this->recompense;
    }

    public function setRecompense(string $recompense): static
    {
        $this->recompense = $recompense;

        return $this;
    }

    /**
     * @return Collection<int, TaskAssignment>
     */
    public function getTaskAssignments(): Collection
    {
        return $this->taskAssignments;
    }

    public function addTaskAssignment(TaskAssignment $taskAssignment): static
    {
        if (!$this->taskAssignments->contains($taskAssignment)) {
            $this->taskAssignments->add($taskAssignment);
            $taskAssignment->setTask($this);
        }

        return $this;
    }

    public function removeTaskAssignment(TaskAssignment $taskAssignment): static
    {
        if ($this->taskAssignments->removeElement($taskAssignment)) {
            // set the owning side to null (unless already changed)
            if ($taskAssignment->getTask() === $this) {
                $taskAssignment->setTask(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
