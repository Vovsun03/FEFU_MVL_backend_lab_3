<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Entity\Project;
use App\Form\GroupType;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GroupController extends AbstractController
{
    #[Route('/groups', name: 'group_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $group = new ProjectGroup();
        $form = $this->createForm(GroupType::class, $group);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($group);
            $em->flush();
            return $this->json(['data' => $group], 201);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/groups/{id}', name: 'group_read', methods: ['GET'])]
    public function read(ProjectGroup $group): JsonResponse
    {
        return $this->json(['data' => $group]);
    }

    #[Route('/groups', name: 'group_list', methods: ['GET'])]
    public function list(ProjectGroupRepository $projectGroupRepository): JsonResponse
    {
        $projectGroups = $projectGroupRepository->findAll();
        $data = array_map(function (ProjectGroup $projectGroup) {
            return [
                'id' => $projectGroup->getId(),
                'name' => $projectGroup->getName(),
                'createdAt' => $projectGroup->getCreatedAt(),
                'updatedAt' => $projectGroup->getUpdatedAt(),
                'projects' => array_map(function (Project $project) {
                    return [
                        'id' => $project->getId(),
                        'name' => $project->getName(),
                    ];
                }, $projectGroup->getProjects()->toArray()),
            ];
        }, $projectGroups);
    
        return $this->json(['data' => $data]);
    }

    #[Route('/groups/{id}', name: 'group_update', methods: ['PATCH'])]
    public function update(ProjectGroup $group, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $group->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->json(['data' => $group]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }
        return $this->json(['errors' => $errors], 400);
    }

    #[Route('/groups/{id}', name: 'group_delete', methods: ['DELETE'])]
    public function delete(ProjectGroup $group, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($group);
        $em->flush();
        return $this->json(['data' => 'Group deleted']);
    }
}
