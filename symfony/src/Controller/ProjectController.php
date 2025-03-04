<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'project_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($project);
            $em->flush();
            return $this->json(['data' => $project], 201);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/projects/{id}', name: 'project_read', methods: ['GET'])]
    public function read(Project $project): JsonResponse
    {
        return $this->json(['data' => $project]);
    }

    #[Route('/projects', name: 'project_list', methods: ['GET'])]
    public function list(ProjectRepository $projectRepository): JsonResponse
    {
        $projects = $projectRepository->findAll();
        $data = array_map(function (Project $project) {
            return [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'createdAt' => $project->getCreatedAt(),
                'updatedAt' => $project->getUpdatedAt(),
                'projectGroup' => [
                    'id' => $project->getProjectGroup()->getId(),
                    'name' => $project->getProjectGroup()->getName(),
                ],
                'tasks' => array_map(function (Task $task) {
                    return [
                        'id' => $task->getId(),
                        'name' => $task->getName(),
                    ];
                }, $project->getTasks()->toArray()),
            ];
        }, $projects);
    
        return $this->json(['data' => $data]);
    }

    #[Route('/projects/{id}', name: 'project_update', methods: ['PATCH'])]
    public function update(Project $project, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $project->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->json(['data' => $project]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/projects/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(Project $project, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($project);
        $em->flush();
        return $this->json(['data' => 'Project deleted']);
    }
}
