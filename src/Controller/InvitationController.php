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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvitationController extends AbstractController
{

    #[Route('/invitation', name: 'app_invitation_new')]
public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    $form = $this->createForm(InvitationType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $invitation = $form->getData();
        $invitation->setSender($this->getUser());
        $invitation->setToken(bin2hex(random_bytes(32)));

        $entityManager->persist($invitation);
        $entityManager->flush();

        $email = (new Email())
            ->from('admin@brainchat.com')
            ->to($invitation->getRecipient()->getEmail())
            ->subject('Invitation à rejoindre une salle de chat')
            ->html($this->renderView('invitation/email.html.twig', [
                'invitation' => $invitation,
            ]));

        $mailer->send($email);

        return $this->redirectToRoute('app_invitation_index');
    }

    return $this->render('invitation/index.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/invitations', name: 'app_invitation_index', methods: ['GET'])]
    public function index(InvitationRepository $invitationRepository): Response
    {
        return $this->render('invitation/list.html.twig', [
            'invitations' => $invitationRepository->findAll(),
        ]);
    }
}