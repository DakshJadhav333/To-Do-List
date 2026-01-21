<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index')]
    public function index(TaskRepository $repo): Response
    {
        $tasks = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'total' => count($tasks),
            'completed' => $repo->countCompleted(),
        ]);
    }

    #[Route('/new', name: 'task_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Task added successfully');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/form.html.twig', [
            'form' => $form,
            'title' => 'Add Task',
        ]);
    }

    #[Route('/{id}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Task updated');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/form.html.twig', [
            'form' => $form,
            'title' => 'Edit Task',
        ]);
    }

    #[Route('/{id}/delete', name: 'task_delete', methods: ['POST'])]
    public function delete(?Task $task, Request $request, EntityManagerInterface $em): Response
    {
    if (!$task) {
        return $this->redirectToRoute('task_index');
    }

    if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
        $em->remove($task);
        $em->flush();
    }

    return $this->redirectToRoute('task_index');
    }


    #[Route('/{id}/toggle', name: 'task_toggle', methods: ['POST'])]
    public function toggle(Task $task, EntityManagerInterface $em): Response
    {
        $task->setCompleted(!$task->isCompleted());
        $em->flush();

        return $this->redirectToRoute('task_index');
    }

}
