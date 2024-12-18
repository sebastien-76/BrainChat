<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ChatMessage;
use App\Service\GroqService;
use App\Form\ChatMessageType;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChatMessageRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat_index')]
    public function index(RoomRepository $roomRepository, TokenInterface $token): Response
    {
        $user = $token->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
            $userRole = $user->getRoles();
        }
        $rooms = $roomRepository->findAll();
        if (in_array('ROLE_ADMIN', $userRole)) {
            $privateRooms = $roomRepository->findAllPrivateRooms();
        } else {
            $privateRooms = $roomRepository->findPrivateRooms($userId);
        }

        return $this->render('chat/index.html.twig', [
            'privateRooms' => $privateRooms,
            'rooms' => $rooms,
        ]);
    }

    #[Route('/chat/{roomId}', name: 'app_chat_show', requirements: ['roomId' => '\d+'])]
    public function showChat(int $roomId, RoomRepository $roomRepository, TokenInterface $token, ChatMessageRepository $chatMessageRepository, Request $request, EntityManagerInterface $em): Response
    {
        $user = $token->getUser();
        if ($user instanceof User) {
            $userId = $user->getId();
            $userRole = $user->getRoles();
        }
        $room = $roomRepository->find($roomId);
        $rooms = $roomRepository->findAll();

        if (in_array('ROLE_ADMIN', $userRole)) {
            $privateRooms = $roomRepository->findAllPrivateRooms();
        } else {
            $privateRooms = $roomRepository->findPrivateRooms($userId);
        }

        if (!$room) {
            throw $this->createNotFoundException("Room non trouvée");
        }

        $user = $token->getUser();

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

            return $this->redirectToRoute('app_chat_show', ['roomId' => $roomId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat/show.html.twig', [
            'privateRooms' => $privateRooms,
            'rooms' => $rooms,
            'currentRoom' => $room,
            'chatMessages' => $chatMessageRepository->findBy(['Room' => $room]),
            'form' => $form,
            'chatMessage' => $chatMessage
        ]);
    }


    #[Route('/groq/{roomId}/{questionId}', name: 'groq_chat')]
    public function chat(int $roomId, int $questionId, GroqService $groqService, RoomRepository $roomRepository, UserRepository $userRepository, ChatMessageRepository $chatMessageRepository, EntityManagerInterface $em): Response
    {
        $availableQuestions = [
            1 => "Peux-tu me faire un résumé clair et concis ?",
            2 => "Peux tu me donner des idées ou pistes de réflexion en fonction du contexte de la discussion",
            3 => "Peux-tu analyser cette conversation ?",
            4 => "Peux tu regarder le dernier message du chat et repondre à la question?",
        ];


        $messages = $chatMessageRepository->findBy(['Room' => $roomId]);
        $lastMessage = end($messages);

        $chatHistory = [];
        if ($questionId === 4 && $lastMessage) {
            // Pour la question 4, on n'envoie que le dernier message
            $chatHistory[] = [
                'role' => 'user',
                'content' => $lastMessage->getContent(),
            ];
        } else {
            // Pour les autres questions, on envoie tout l'historique
            foreach ($messages as $message) {
                $chatHistory[] = [
                    'role' => 'user',
                    'content' => $message->getContent(),
                ];
            }
        }

        $selectedQuestion = $availableQuestions[$questionId] ?? $availableQuestions[1];


        $groqUser = $userRepository->findOneBy(['email' => 'groq@example.com']);

        $response = $groqService->generateResponse($chatHistory);
        $room = $roomRepository->find($roomId);
        $chatMessage = new ChatMessage();
        $chatMessage->setContent($response['content']);
        $chatMessage->setRoom($room);
        $chatMessage->setUser($groqUser);

        // Persister et sauvegarder le message
        $em->persist($chatMessage);
        $em->flush();

        // Rediriger vers le chat
        return $this->redirectToRoute('app_chat_show', ['roomId' => $roomId]);
    }
}
