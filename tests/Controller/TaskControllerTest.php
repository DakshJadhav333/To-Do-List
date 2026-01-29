<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        // Ensure we have a logged-in user for /tasks/* routes.
        $this->loginAsTestUser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }

    private function loginAsTestUser(): void
    {
        // Find or create a simple user
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
        if (!$user) {
            $user = new User();
            $user->setEmail('test@example.com');

            // If your User requires a hashed password and unique fields/roles, set them appropriately.
            // For tests using loginUser(), password does not matter because we bypass form_login.
            // Example if you have roles:
            // $user->setRoles(['ROLE_USER']);

            // If your User entity requires non-null fields (e.g., username), set them here.

            $user->setPassword('dummy');
            $this->em->persist($user);
            $this->em->flush();
        }

        // Programmatic login (bypasses the actual login form)
        $this->client->loginUser($user);
    }

    public function testCreateTask(): void
    {
        $crawler = $this->client->request('GET', '/tasks/new');
        $this->assertResponseIsSuccessful(); // now should be 200 because we are logged in

        // Adjust button label and field names to match your TaskType form
        $form = $crawler->selectButton('Save')->form([
            'task[title]' => 'Test Task',
            'task[priority]' => 'HIGH',
        ]);

        $this->client->submit($form);

        // If your controller redirects after successful save
        if ($this->client->getResponse()->isRedirect()) {
            $this->client->followRedirect();
        }

        $task = $this->em->getRepository(Task::class)->findOneBy(['title' => 'Test Task']);
        $this->assertNotNull($task);
        $this->assertSame('HIGH', $task->getPriority());
    }

    public function testEditTask(): void
    {
        // Prepare an existing task
        $task = new Task();
        $task->setTitle('Old Title');
        $task->setPriority('LOW');
        $this->em->persist($task);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertResponseIsSuccessful();

        // Adjust button label if your edit template uses 'Update' instead of 'Save'
        $form = $crawler->selectButton('Save')->form([
            'task[title]' => 'Updated Title',
            'task[priority]' => 'MEDIUM',
        ]);

        $this->client->submit($form);

        if ($this->client->getResponse()->isRedirect()) {
            $this->client->followRedirect();
        }

        $this->em->clear();
        $updatedTask = $this->em->getRepository(Task::class)->find($task->getId());

        $this->assertSame('Updated Title', $updatedTask->getTitle());
        $this->assertSame('MEDIUM', $updatedTask->getPriority());
    }

    public function testDeleteTask(): void
    {
        // Prepare a task to delete
        $task = new Task();
        $task->setTitle('Delete Me');
        $task->setPriority('LOW');
        $this->em->persist($task);
        $this->em->flush();

        // Generate CSRF token exactly as your controller expects.
        // Example in controller: isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))
        $csrfManager = static::getContainer()->get('security.csrf.token_manager');
        $token = $csrfManager->getToken('delete'.$task->getId())->getValue();

        // Send POST delete with valid token
        $this->client->request('POST', '/tasks/'.$task->getId().'/delete', [
            '_token' => $token,
        ]);

        // Many delete actions redirect; follow if needed
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->em->clear();
        $deletedTask = $this->em->getRepository(Task::class)->find($task->getId());
        $this->assertNull($deletedTask);
    }
}