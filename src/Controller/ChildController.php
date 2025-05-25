<?php

namespace App\Controller;

use App\Entity\TaskAssignment;
use App\Repository\TaskAssignmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/child')]
final class ChildController extends AbstractController
{
    #[Route('/dashboard', name: 'app_child_dashboard')]
    public function dashboard(TaskAssignmentRepository $repository): Response
    {
        // récupérer les taches assigner à l'enfants
        $inProgressTasks = $repository->findInProgressTasksByChild($this->getUser());
        
        // grouper les tâches 
        $pendingTasks = array_filter($inProgressTasks, function($task) {
            return $task->getStatus() === TaskAssignment::STATUS_PENDING;
        });
        
        $acceptedTasks = array_filter($inProgressTasks, function($task) {
            return $task->getStatus() === TaskAssignment::STATUS_ACCEPTED;
        });
        
        // récupérer les tâches urgent
        $urgentTasks = array_filter($inProgressTasks, function($task) {
            return $task->getTask()->getIsUrgent();
        });
        
        return $this->render('child/dashboard.html.twig', [
            'pending_tasks' => $pendingTasks,
            'accepted_tasks' => $acceptedTasks,
            'urgent_tasks' => $urgentTasks,
        ]);
    }
    
    #[Route('/tasks', name: 'app_child_tasks')]
    public function tasks(
        TaskAssignmentRepository $repository,
        Request $request
    ): Response
    {
        // filtrer  requete 
        $filter = $request->query->get('filter', 'progress');
        
        // récupérer les tâches filtré 
        switch ($filter) {
            case 'completed':
                $tasks = $repository->findByChildAndStatus($this->getUser(), TaskAssignment::STATUS_COMPLETED);
                break;
            case 'refused':
                $tasks = $repository->findByChildAndStatus($this->getUser(), TaskAssignment::STATUS_REFUSED);
                break;
            case 'negotiating':
                $tasks = $repository->findByChildAndStatus($this->getUser(), TaskAssignment::STATUS_NEGOTIATING);
                break;
            case 'progress':
            default:
                $tasks = $repository->findInProgressTasksByChild($this->getUser());
                break;
        }
        
        return $this->render('child/tasks.html.twig', [
            'tasks' => $tasks,
            'filter' => $filter,
        ]);
    }
    
    #[Route('/tasks/{id}/accept', name: 'app_child_task_accept', methods: ['POST'])]
    public function acceptTask(
        TaskAssignment $taskAssignment,
        EntityManagerInterface $entityManager
    ): Response
    {
        // vérfier l'assifnement de tâche appartient à l'enfants
        if ($taskAssignment->getChild() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’êtes pas autorisé à accepter cette tâche');
        }
        
        // accepter que pending task
        if ($taskAssignment->getStatus() !== TaskAssignment::STATUS_PENDING) {
            $this->addFlash('error', 'Vous ne pouvez accepter que les tâches en attente');
            return $this->redirectToRoute('app_child_tasks');
        }
        
        $taskAssignment->setStatus(TaskAssignment::STATUS_ACCEPTED);
        $entityManager->flush();
        
        $this->addFlash('success', 'tâche accepté');
        
        return $this->redirectToRoute('app_child_tasks');
    }
    
    #[Route('/tasks/{id}/refuse', name: 'app_child_task_refuse', methods: ['POST'])]
    public function refuseTask(
        TaskAssignment $taskAssignment,
        EntityManagerInterface $entityManager
    ): Response
    {
        // verfier tâche == child 
        if ($taskAssignment->getChild() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’êtes pas autorisé à refuser cette tâche');
        }
        
        // autorise le refus seulement task pending 
        if ($taskAssignment->getStatus() !== TaskAssignment::STATUS_PENDING) {
            $this->addFlash('error', 'Vous ne pouvez refuser que les tâches en attente');
            return $this->redirectToRoute('app_child_tasks');
        }
        
        $taskAssignment->setStatus(TaskAssignment::STATUS_REFUSED);
        $entityManager->flush();
        
        $this->addFlash('success', 'Tâche réfusé');
        
        return $this->redirectToRoute('app_child_tasks');
    }
    
    #[Route('/tasks/{id}/negotiate', name: 'app_child_task_negotiate', methods: ['POST'])]
    public function negotiateTask(
        TaskAssignment $taskAssignment,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        if ($taskAssignment->getChild() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’êtes pas autorisé à négocier cette tâche');
        }
        
        if ($taskAssignment->getStatus() !== TaskAssignment::STATUS_PENDING) {
            $this->addFlash('error', 'Vous ne pouvez négocier que les tâches en attente');
            return $this->redirectToRoute('app_child_tasks');
        }
        
        $note = $request->request->get('note');
        
        if (!$note) {
            $this->addFlash('error', 'Veuillez fournir une raison pour la négociation');
            return $this->redirectToRoute('app_child_task_show', ['id' => $taskAssignment->getId()]);
        }
        
        $taskAssignment->setStatus(TaskAssignment::STATUS_NEGOTIATING);
        $taskAssignment->setNotes($note);
        $entityManager->flush();
        
        $this->addFlash('success', 'Négociation des tâches envoyée à Parent');
        
        return $this->redirectToRoute('app_child_tasks');
    }
    
    #[Route('/tasks/{id}/complete', name: 'app_child_task_complete', methods: ['POST'])]
    public function completeTask(
        TaskAssignment $taskAssignment,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($taskAssignment->getChild() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’êtes pas autorisé à effectuer cette tâche');
        }

        if ($taskAssignment->getStatus() !== TaskAssignment::STATUS_ACCEPTED) {
            $this->addFlash('error', 'Vous ne pouvez effectuer que les tâches acceptées');
            return $this->redirectToRoute('app_child_tasks');
        }
        
        $taskAssignment->setStatus(TaskAssignment::STATUS_COMPLETED);
        $taskAssignment->setCompletedAt(new \DateTimeImmutable());
        $entityManager->flush();
        
        $this->addFlash('success', 'Tâche accomplie');
        
        return $this->redirectToRoute('app_child_tasks');
    }
    
    #[Route('/tasks/{id}', name: 'app_child_task_show')]
    public function showTask(TaskAssignment $taskAssignment): Response
    {
        if ($taskAssignment->getChild() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to view this task');
        }
        
        return $this->render('child/task_detail.html.twig', [
            'task_assignment' => $taskAssignment,
        ]);
    }
}
