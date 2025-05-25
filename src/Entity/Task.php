<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{ #[ORM\Id]
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

    #[ORM\Column]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reward = null;

    #[ORM\ManyToOne(inversedBy: 'createdTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $parent = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskAssignment::class, cascade: ['persist', 'remove'])]
    private Collection $taskAssignments;

    #[ORM\Column]
    private bool $isUrgent = false;

    public function __construct()
    {
        $this->taskAssignments = new ArrayCollection();
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

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getReward(): ?string
    {
        return $this->reward;
    }

    public function setReward(?string $reward): self
    {
        $this->reward = $reward;

        return $this;
    }

    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function setParent(?User $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, TaskAssignment>
     */
    public function getTaskAssignments(): Collection
    {
        return $this->taskAssignments;
    }

    public function addTaskAssignment(TaskAssignment $taskAssignment): self
    {
        if (!$this->taskAssignments->contains($taskAssignment)) {
            $this->taskAssignments->add($taskAssignment);
            $taskAssignment->setTask($this);
        }

        return $this;
    }

    public function removeTaskAssignment(TaskAssignment $taskAssignment): self
    {
        if ($this->taskAssignments->removeElement($taskAssignment)) {
            // set the owning side to null (unless already changed)
            if ($taskAssignment->getTask() === $this) {
                $taskAssignment->setTask(null);
            }
        }

        return $this;
    }

    public function getIsUrgent(): bool
    {
        return $this->isUrgent;
    }

    public function setIsUrgent(bool $isUrgent): self
    {
        $this->isUrgent = $isUrgent;

        return $this;
    }

    /**
     * Get the remaining time for the task
     * 
     * @return string Formatted remaining time
     */
    public function getRemainingTime(): string
    {
        $now = new \DateTimeImmutable();
        $interval = $this->dueDate->diff($now);
        
        if ($this->dueDate < $now) {
            return 'Overdue';
        }
        
        if ($interval->days > 0) {
            return $interval->format('%d days, %h hours');
        }
        
        return $interval->format('%h hours, %i minutes');
    }

    /**
     * Check if the task is close to its deadline
     * 
     * @return bool
     */
    public function isCloseToDeadline(): bool
    {
        $now = new \DateTimeImmutable();
        $interval = $this->dueDate->diff($now);
        
        return $interval->days === 0 && $interval->h < 12;
    }
}