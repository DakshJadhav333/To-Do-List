<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskManager;
use App\Service\TaskQuery;
use Doctrine\ORM\EntityManagerInterface; // (no longer used directly except via services)
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index')]
    public function index(Request $request, TaskQuery $query): Response
    {
        $page   = max(1, (int) $request->query->get('page', 1));
        $search = $request->query->get('q');
        $limit  = 5;

        $data = $query->getPageData($page, $limit, $search);
        $totalPages = (int) ceil($data['total'] / $limit);

        return $this->render('task/index.html.twig', [
            'tasks'       => $data['tasks'],
            'total'       => $data['total'],
            'completed'   => $data['completed'],
            'search'      => $search,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'limit'       => $limit,
        ]);
    }

    #[Route('/new', name: 'task_new')]
    public function new(Request $request, TaskManager $manager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->create($task);
            $this->addFlash('success', 'Task added successfully');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/form.html.twig', [
            'form'  => $form,
            'title' => 'Add Task',
        ]);
    }

    #[Route('/{id}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request, TaskManager $manager): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->update($task);
            $this->addFlash('success', 'Task updated');
            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/form.html.twig', [
            'form'  => $form,
            'title' => 'Edit Task',
        ]);
    }

    #[Route('/{id}/delete', name: 'task_delete', methods: ['POST'])]
    public function delete(?Task $task, Request $request, TaskManager $manager): Response
    {
        if ($task && $this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $manager->delete($task);
        }

        return $this->redirectToRoute('task_index');
    }

    #[Route('/{id}/toggle', name: 'task_toggle', methods: ['POST'])]
    public function toggle(Task $task, TaskManager $manager): Response
    {
        $manager->toggle($task);
        return $this->redirectToRoute('task_index');
    }
}