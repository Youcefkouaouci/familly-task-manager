<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function home(): Response
    {
        // si l'utilisateur est login rediregier vers la dashboard approprie
        if ($this->getUser()) {

            if (in_array('ROLE_PARENT', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_parent_dashboard');
            }
            return $this->redirectToRoute('app_child_dashboard');
        }
        
        return $this->render('index/home.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
