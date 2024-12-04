<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\ChatMessage;
use App\Form\ChatMessageType;
use App\Repository\RoomRepository;
use App\Repository\ChatMessageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{
    #[Route('/chat/{id}', name: 'app_chat_show', requirements: ['id' => '\d+'], defaults: ['id' => 61])]
    public function showChat(int $id, RoomRepository $roomRepository, ChatMessageRepository $chatMessageRepository): Response
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            throw $this->createNotFoundException("Room non trouvée");
        }

        $form = $this->createForm(ChatMessageType::class, new ChatMessage(), [
            'action' => $this->generateUrl('app_chat_message_new', ['id' => $id])

        ]);

        return $this->render('chat/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
            'currentRoom' => $room,
            'chat_messages' => $chatMessageRepository->findBy(['Room' => $room]),
            'form' => $form->createView(),
        ]);
    }
}
