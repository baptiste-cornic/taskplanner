<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    public function __construct(private TaskRepository $taskRepo){
    }

    #[Route('/tasklist', name: 'task_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $tasks = $this->taskRepo->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/{id}', name: 'task', requirements: ['id' => '[0-9]+'])]
    public function task($id = null): Response
    {
        $task = $this->taskRepo->find($id);
       // dd($task);
        return $this->render('task/task.html.twig', [
            'task' => $task,
        ]);
    }
}
