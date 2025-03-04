<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($task);
            $em->flush();
            return $this->json(['data' => $task], 201);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/tasks/{id}', name: 'task_read', methods: ['GET'])]
    public function read(Task $task): JsonResponse
    {
        return $this->json(['data' => $task]);
    }

    #[Route('/tasks', name: 'task_list', methods: ['GET'])]
    public function list(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findAll();
        $data = array_map(function (Task $task) {
            return [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'createdAt' => $task->getCreatedAt(),
                'updatedAt' => $task->getUpdatedAt(),
                'project' => [
                    'id' => $task->getProject()->getId(),
                    'name' => $task->getProject()->getName(),
                ],
            ];
        }, $tasks);
    
        return $this->json(['data' => $data]);
    }

    #[Route('/tasks/{id}', name: 'task_update', methods: ['PATCH'])]
    public function update(Task $task, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $task->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->json(['data' => $task]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/tasks/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($task);
        $em->flush();
        return $this->json(['data' => 'Task deleted']);
    }
}
