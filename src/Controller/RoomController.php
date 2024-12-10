<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Entity\Participant;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/room')]
final class RoomController extends AbstractController
{
    #[Route(name: 'app_room_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_room_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant = new Participant();
            $participant->setUser($this->getUser());
            $participant->setRoom($room);
            $participant->setRoles(['ROLE_MODERATOR']);
            $entityManager->persist($room);
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_index', [],Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant = new Participant();
            $participant->setUser($this->getUser());
            $participant->setRoom($room);
            $participant->setRoles(['ROLE_MODERATOR']); // Ajouter cette propriété dans l'entité Participant

            $entityManager->persist($room);
            $entityManager->persist($participant);
            $entityManager->flush();

            $entityManager->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $room->getId()],  Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_room_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/users', name: 'app_room_users', methods: ['GET'])]
    public function showUsers(Room $room): Response
    {
        $users = $room->getParticipants();

        return $this->render('room/users.html.twig', [
            'room' => $room,
            'users' => $users,
        ]);
    }
}
