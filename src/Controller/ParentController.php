<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskAssignment;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskAssignmentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/parent')]
final class ParentController extends AbstractController
{
    /*
    Dans ce controlleur,
        peut être je vais ajouter route pour l'acceuil et l'inscription 

        route pour dashboard des parents
        route pour les tasks + fonctionne pour count tasks selon leur status pendant une periode precise

        route pour ajouter nouvelles tasks
        route pour afficher les détails de tasks 
        route pour modifier tasks
        route 
    */
    // dashboard pour les parents pour afficher les taches de la semaine 
    #[Route('/dashboard', name: 'app_parent_dashboard')]
    public function dashboard(
        TaskRepository $taskRepository,
        TaskAssignmentRepository $taskAssignmentRepository,
    ): Response
    {
        /*
        récupérer l'utilisateur Rôle Parents
        récupèrer les tâches 
        counter les tâche par leur status 
        j'affiche les tâche dans ma templete twig selon le stauts des tâches 
        */

        /** @var User $user */
        $user = $this->getUser();
        
        // récupèrer les tâche de la semaine 
        $weeklyTasks = $taskRepository->findByParentForCurrentWeek($user);
        
        // compter les tâche selon leur status
        $pendingCount = $this->getTaskCountByStatus($taskAssignmentRepository, TaskAssignment::STATUS_PENDING);
        $acceptedCount = $this->getTaskCountByStatus($taskAssignmentRepository, TaskAssignment::STATUS_ACCEPTED);
        $completedCount = $this->getTaskCountByStatus($taskAssignmentRepository, TaskAssignment::STATUS_COMPLETED);
        $refusedCount = $this->getTaskCountByStatus($taskAssignmentRepository, TaskAssignment::STATUS_REFUSED);
        $negotiatingCount = $this->getTaskCountByStatus($taskAssignmentRepository, TaskAssignment::STATUS_NEGOTIATING);
        
        // les tâches urgents
        $urgentTasks = $taskRepository->findUrgentTasks();
        
        return $this->render('parent/dashboard.html.twig', [
            'weekly_tasks' => $weeklyTasks,
            'pending_count' => $pendingCount,
            'accepted_count' => $acceptedCount,
            'completed_count' => $completedCount,
            'refused_count' => $refusedCount,
            'negotiating_count' => $negotiatingCount,
            'urgent_tasks' => $urgentTasks,
        ]);
    }
    private function getTaskCountByStatus(TaskAssignmentRepository $repository, string $status): int
    {
        $assignments = $repository->findBy(['status' => $status]);
        return count($assignments);
    }

    #[Route('/tasks', name: 'app_parent_tasks')]
    public function tasks(
        TaskRepository $taskRepository,
        Request $request
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        // récupération des données avec filter semaine _ moins 
        $filter = $request->query->get('filter', 'week');
        
        if ($filter === 'month') {
            $tasks = $taskRepository->findByParentForCurrentMonth($user);
        } else {
            $tasks = $taskRepository->findByParentForCurrentWeek($user);
        }
        
        return $this->render('parent/tasks.html.twig', [
            'tasks' => $tasks,
            'filter' => $filter,
        ]);
    }

     #[Route('/tasks/new', name: 'app_parent_tasks_new')]
    public function newTask(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $task = new Task();
        $task->setParent($user);
        
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the selected children
            $selectedChildren = $form->get('children')->getData();
            
            // Save the task
            $entityManager->persist($task);
            
            // Create task assignments for each selected child
            foreach ($selectedChildren as $child) {
                $assignment = new TaskAssignment();
                $assignment->setTask($task);
                $assignment->setChild($child);
                $assignment->setStatus(TaskAssignment::STATUS_PENDING);
                
                $entityManager->persist($assignment);
                $task->addTaskAssignment($assignment);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Task created successfully!');
            
            return $this->redirectToRoute('app_parent_tasks');
        }
        
        // récuperer tous les enfants 
        $children = $userRepository->findAllChildren();
        
        return $this->render('parent/new_task.html.twig', [
            'form' => $form->createView(),
            'children' => $children,
        ]);
    }


}
