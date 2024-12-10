<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\Invitation;
use App\Form\InvitationType;
use Symfony\Component\Mime\Email;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Repository\InvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvitationController extends AbstractController
{

    #[Route('/invitation', name: 'app_invitation_new')]
    public function new(UserRepository $userRepository, RoomRepository $roomRepository): Response
    {
        $users = $userRepository->findAll();
        $rooms = $roomRepository->findAll();

        $form = $this->createForm(InvitationType::class);
        return $this->render('invitation/index.html.twig', [
            'form' => $form->createView(),
            'users' =>$users,
            'rooms' => $rooms,
        ]);
        return $this->render('invitation/index.html.twig');
    }


    #[Route('/invitations', name: 'app_invitation_index', methods: ['GET'])]
    public function index(InvitationRepository $invitationRepository): Response
    {
        return $this->render('invitation/list.html.twig', [
            'invitations' => $invitationRepository->findAll(),
        ]);
    }
}