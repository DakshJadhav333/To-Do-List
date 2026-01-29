<?php

namespace App\Tests\Unit\Service;

use App\Entity\Task;
use App\Service\TaskManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskManagerTest extends TestCase
{
    public function testCreatePersistsAndFlushes(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);

        $em->expects($this->once())->method('persist')->with($this->isInstanceOf(Task::class));
        $em->expects($this->once())->method('flush');

        $manager = new TaskManager($em);

        $task = (new Task())->setTitle('T1')->setPriority('HIGH');
        $result = $manager->create($task);

        $this->assertSame($task, $result);
        $this->assertSame('T1', $result->getTitle());
        $this->assertSame('HIGH', $result->getPriority());
    }

    public function testUpdateFlushesOnly(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->never())->method('persist');
        $em->expects($this->once())->method('flush');

        $manager = new TaskManager($em);
        $task = (new Task())->setTitle('Old')->setPriority('LOW');

        $task->setTitle('New')->setPriority('MEDIUM');
        $result = $manager->update($task);

        $this->assertSame('New', $result->getTitle());
        $this->assertSame('MEDIUM', $result->getPriority());
    }

    public function testDeleteRemovesAndFlushes(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $task = new Task();

        $em->expects($this->once())->method('remove')->with($task);
        $em->expects($this->once())->method('flush');

        $manager = new TaskManager($em);
        $manager->delete($task);

        $this->assertTrue(true); // expectations verified
    }

    public function testToggleFlipsCompletedAndFlushes(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('flush');

        $manager = new TaskManager($em);

        $task = (new Task())->setCompleted(false);
        $manager->toggle($task);

        $this->assertTrue($task->isCompleted());
    }
}