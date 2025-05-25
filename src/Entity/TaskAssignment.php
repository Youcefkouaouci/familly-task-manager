<?php

namespace App\Entity;

use App\Repository\TaskAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskAssignmentRepository::class)]
class TaskAssignment
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REFUSED = 'refused';
    const STATUS_NEGOTIATING = 'negotiating';
    const STATUS_COMPLETED = 'completed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(length: 255)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?bool $notified = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastStatusChangedAt = null;

    #[ORM\ManyToOne(inversedBy: 'taskAssignments')]
    private ?Task $Task = null;

    #[ORM\ManyToOne(inversedBy: 'assignedTasks')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function isNotified(): ?bool
    {
        return $this->notified;
    }

    public function setNotified(bool $notified): static
    {
        $this->notified = $notified;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getLastStatusChangedAt(): ?\DateTimeImmutable
    {
        return $this->lastStatusChangedAt;
    }

    public function setLastStatusChangedAt(\DateTimeImmutable $lastStatusChangedAt): static
    {
        $this->lastStatusChangedAt = $lastStatusChangedAt;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->Task;
    }

    public function setTask(?Task $Task): static
    {
        $this->Task = $Task;

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
