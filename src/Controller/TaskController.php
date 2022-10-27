<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use App\Service\UrgencyService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    public function __construct(private TaskRepository $taskRepo, private CategoryRepository $categoryRepo){
    }

    #[Route('/tasklist', name: 'task_list')]
    public function index(ManagerRegistry $doctrine, UrgencyService $urgencyService): Response
    {
        $tasks = $this->taskRepo->findAll();
        $categories = $this->categoryRepo->findAll();
        $tasks = $urgencyService->getUrgency($tasks);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'categories' => $categories,
        ]);
    }

    #[Route('/task/{id}', name: 'task', requirements: ['id' => '[0-9]+'])]
    public function task($id = null): Response
    {
        $task = $this->taskRepo->find($id);

        return $this->render('task/task.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/search', name: 'search', methods: ['POST'])]
    public function search(ManagerRegistry $doctrine,string $keyword = null, Request $request): Response
    {
        $keyword = $request->get('search');

        if ($keyword)
            $tasks = $this->taskRepo->findWithKeyWord($keyword);

        $categories = $this->categoryRepo->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks??null,
            'categories' => $categories??null,
        ]);
    }

    #[Route('/task/delete/{id}', name: 'delete_task', requirements: ['id' => '[0-9]+'])]
    public function deleteTask($id = null, EntityManagerInterface $em): Response
    {
        $task = $this->taskRepo->find($id);

        if (!$this->isGranted('ROLE_ADMIN')){
            $this->addFlash('error', 'Interdit de supprimer' );
            return $this->redirectToRoute('task', ['id' => $id]);
        }
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('task_list');
    }
}
