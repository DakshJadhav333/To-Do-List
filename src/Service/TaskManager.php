<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{
    public function __construct(private EntityManagerInterface $em) {}

    public function create(Task $task): Task
    {
        $this->em->persist($task);
        $this->em->flush();
        return $task;
    }

    public function update(Task $task): Task
    {
        // All changes are already on $task (via form), just flush
        $this->em->flush();
        return $task;
    }

    public function delete(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }

    public function toggle(Task $task): Task
    {
        $task->setCompleted(!$task->isCompleted());
        $this->em->flush();
        return $task;
    }
}