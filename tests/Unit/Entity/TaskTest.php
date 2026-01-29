<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $task = new Task();
        $task->setTitle('X')->setPriority('HIGH')->setCompleted(true);

        $this->assertSame('X', $task->getTitle());
        $this->assertSame('HIGH', $task->getPriority());
        $this->assertTrue($task->isCompleted());
    }
}