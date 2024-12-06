<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ChatMessage;
use App\Service\GroqService;
use App\Form\ChatMessageType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChatMessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat_index')]
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();

        return $this->render('chat/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/chat/{id}', name: 'app_chat_show', requirements: ['id' => '\d+'])]
    public function showChat(int $id, RoomRepository $roomRepository, TokenInterface $token, ChatMessageRepository $chatMessageRepository, Request $request, EntityManagerInterface $em): Response
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

        return $this->render('chat/show.html.twig', [
            'rooms' => $roomRepository->findAll(),
            'currentRoom' => $room,
            'chatMessages' => $chatMessageRepository->findBy(['Room' => $room]),
            'form' => $form,
            'chatMessage' => $chatMessage
        ]);
    }

    #[Route('/chat/history/{roomId}', name: 'chat_history')]
    public function getChatHistory(int $roomId, ChatMessageRepository $chatMessageRepository): JsonResponse
    {
        $messages = $chatMessageRepository->findBy(['Room' => $roomId]);

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'user' => $message->getUser()->getAuthor(),
                'content' => $message->getContent(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/groq', name: 'groq_chat')]
    public function chat2(GroqService $groqService, UserRepository $userRepository, RoomRepository $roomRepository, ChatMessageRepository $chatMessageRepository, EntityManagerInterface $em): Response
    {

        $url = $_SERVER['HTTP_REFERER'];
        $explodeUrl = explode('/', $url);
        $stringId = end($explodeUrl);
        $id = (int) $stringId;
        
        $messages = $chatMessageRepository->findBy(['Room' => $id]);

        $chatHistory = [];
        foreach ($messages as $message) {
            $chatHistory[] = [
                'role' => 'user',
                'content' => $message->getContent(),
            ];
        }


        $chatHistory[] = [
            'role' => 'user',
            'content' => "Peux tu me faire un resumé ??",
        ];

        $user = $userRepository->find(2);
        $response = $groqService->generateResponse($chatHistory);


        $chatMessageGroq = new ChatMessage();
        $chatMessage = new ChatMessage();
        $form = $this->createForm(
            ChatMessageType::class,
            $chatMessage,
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $chatMessageGroq->setContent($response['content']);
            $chatMessageGroq->setRoom($roomRepository->find($id));
            $chatMessageGroq->setUser($user);        $chatMessageGroq->setContent($response['content']);
            $chatMessageGroq->setRoom($roomRepository->find($id));
            $chatMessageGroq->setUser($user);
            $em->persist($chatMessageGroq);
            $em->flush();
        }



        return $this->render('chat/show.html.twig', [
            'rooms' => $roomRepository->findAll(),
            'currentRoom' => $roomRepository->find($id),
            'chatMessages' => $chatMessageRepository->findBy(['Room' => $id]),
            'form' => $form,
            'chatMessage' => $chatMessageGroq
        ]);
    }
}
