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

#[Route('/message')]
final class ChatMessageController extends AbstractController
{
    #[Route(name: 'app_chat_message_index', methods: ['GET'])]
    public function index(ChatMessageRepository $chatMessageRepository): Response
    {
        return $this->render('chat_message/index.html.twig', [
            'chat_messages' => $chatMessageRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_chat_message_new', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, RoomRepository $roomRepository): Response
    {
        $room = $roomRepository->find($id);
        $chatMessage = new ChatMessage();


        $form = $this->createForm(ChatMessageType::class, $chatMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chatMessage->setRoom($room);
            $entityManager->persist($chatMessage);

            $entityManager->flush();

            return $this->redirectToRoute('app_chat_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_message/new.html.twig', [
            'chat_message' => $chatMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_message_show', methods: ['GET'])]
    public function show(ChatMessage $chatMessage): Response
    {
        return $this->render('chat_message/show.html.twig', [
            'chat_message' => $chatMessage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chat_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChatMessage $chatMessage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatMessageType::class, $chatMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chat_message/edit.html.twig', [
            'chat_message' => $chatMessage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chat_message_delete', methods: ['POST'])]
    public function delete(Request $request, ChatMessage $chatMessage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chatMessage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chatMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chat_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
