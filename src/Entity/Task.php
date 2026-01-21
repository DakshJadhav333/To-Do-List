<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 10)]
    private string $priority = 'LOW';

    #[ORM\Column]
    private bool $completed = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int 
    { 
        return $this->id; 
    }

    public function getTitle(): string 
    { 
        return $this->title; 
    }

    public function setTitle(string $title): self 
    { 
        $this->title = $title; 
        return $this; 
    }

    public function getPriority(): string 
    { 
        return $this->priority; 
    }

    public function setPriority(string $priority): self 
    { 
        $this->priority = $priority; 
        return $this; 
    }

    public function isCompleted(): bool 
    { 
        return $this->completed; 
    }
    public function setCompleted(bool $completed): self 
    { 
        $this->completed = $completed; 
        return $this; 
    }

    public function getCreatedAt(): \DateTimeImmutable 
    { 
        return $this->createdAt; 
    }
}
