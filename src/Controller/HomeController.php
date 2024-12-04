<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $sessionUser = $this->getUser();
        if ($sessionUser instanceof User) {
        return $this->render('home/index.html.twig',
        ['user' => $sessionUser]
    );
    } else {
        return $this->redirectToRoute('app_login');
    };
    }
}
