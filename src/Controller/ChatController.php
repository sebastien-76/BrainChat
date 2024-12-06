<?php

namespace App\Controller;

use App\Entity\ChatMessage;
use App\Form\ChatMessageType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChatMessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ChatController extends AbstractController
{
    #[Route('/chat/{id}', name: 'app_chat_show', requirements: ['id' => '\d+'])]
    public function showChat(int $id, RoomRepository $roomRepository,TokenInterface $token, ChatMessageRepository $chatMessageRepository, Request $request, EntityManagerInterface $em): Response
    {
        $room = $roomRepository->find($id);
        $user = $token->getUser();

        if (!$room) {
            throw $this->createNotFoundException("Room non trouvée");
        }

        $chatMessage = new ChatMessage();
        $form = $this->createForm(
            ChatMessageType::class,
            $chatMessage,
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chatMessage->setRoom($room);
            $chatMessage->setUser($user);
            $em->persist($chatMessage);
            $em->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $id], Response::HTTP_SEE_OTHER);
       }

        return $this->render('chat/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
            'currentRoom' => $room,
            'chatMessages' => $chatMessageRepository->findBy(['Room' => $room]),
            'form' => $form,
            'chatMessage' => $chatMessage
        ]);
    }
}
