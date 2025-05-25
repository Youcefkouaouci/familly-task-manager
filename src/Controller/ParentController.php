<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\TaskAssignmentRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/parent')]
final class ParentController extends AbstractController
{
    // dashboard pour les parents pour afficher les taches de la semaine 
    #[Route('/dashboard', name: 'app_parent_dashboard')]
    public function dashboard(
        TaskRepository $taskRepository,
        TaskAssignmentRepository $taskAssignmentRepository,
    ): Response
    {
        /*
        récupérer l'utilisateur Role Parents
        récupèrer les tâches 
        counter les tâche par leur status 
        j'affiche les tâche dans ma templete twig selon le stauts des tâches 
        */

        /** @var User $user */
        $user = $this->getUser();
        
        // récupèrer les tâche de la semaine 

        // compter les tâche selon leur status

        // les tâches urgents
        
        return $this->render('parent/dashboard.html.twig');
    }
}
