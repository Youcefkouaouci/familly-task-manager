<?php

namespace App\Entity;

use App\Repository\TaskAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskAssignmentRepository::class)]
class TaskAssignment
{    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REFUSED = 'refused';
    const STATUS_NEGOTIATING = 'negotiating';
    const STATUS_COMPLETED = 'completed';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taskAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne(inversedBy: 'assignedTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $child = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?bool $isNotified = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastStatusChangedAt = null;

    public function __construct()
    {
        $this->lastStatusChangedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getChild(): ?User
    {
        return $this->child;
    }

    public function setChild(?User $child): self
    {
        $this->child = $child;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if ($this->status !== $status) {
            $this->lastStatusChangedAt = new \DateTimeImmutable();
        }
        
        $this->status = $status;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getIsNotified(): ?bool
    {
        return $this->isNotified;
    }

    public function setIsNotified(bool $isNotified): self
    {
        $this->isNotified = $isNotified;

        return $this;
    }

    public function getLastStatusChangedAt(): ?\DateTimeImmutable
    {
        return $this->lastStatusChangedAt;
    }

    public function setLastStatusChangedAt(?\DateTimeImmutable $lastStatusChangedAt): self
    {
        $this->lastStatusChangedAt = $lastStatusChangedAt;

        return $this;
    }

    /**
     * Check if the task was completed on time
     */
    public function isCompletedOnTime(): ?bool
    {
        if (!$this->completedAt || $this->status !== self::STATUS_COMPLETED) {
            return null;
        }
        
        return $this->completedAt <= $this->task->getDueDate();
    }

    /**
     * Get the human-readable status
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REFUSED => 'Refused',
            self::STATUS_NEGOTIATING => 'Negotiating',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }

    /**
     * Get the CSS class for the status badge
     */
    public function getStatusClass(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'status-progress',
            self::STATUS_ACCEPTED => 'status-accepted',
            self::STATUS_REFUSED => 'status-rejected',
            self::STATUS_NEGOTIATING => 'status-progress',
            self::STATUS_COMPLETED => 'status-completed',
            default => '',
        };
    }
}