<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\ProjectGroup;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $group = new ProjectGroup();
        $group->setName('Group 1');
        $manager->persist($group);

        $project = new Project();
        $project->setName('Project 1');
        $project->setProjectGroup($group);
        $manager->persist($project);

        $task = new Task();
        $task->setName('Task 1');
        $task->setDescription('Description for Task 1');
        $task->setProject($project);
        $manager->persist($task);

        $manager->flush();
    }
}
