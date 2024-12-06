<?php

namespace App\Twig\Components;

use App\Entity\Room;
use App\Entity\ChatMessage;
use App\Form\ChatMessageType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;



#[AsLiveComponent]
final class Chat extends AbstractController
{

    use DefaultActionTrait;
    use LiveCollectionTrait;

    private RequestStack $requestStack;

    #[LiveProp(writable: true)]
    public array $messages = [];

    #[LiveProp(writable: true, fieldName: 'formdata')]
    public ?ChatMessage $chatMessage;

    #[LiveProp(writable: true)]
    public ?Room $currentRoom;


    public function __construct(private RoomRepository $roomRepository) {}

    private function getId(): int
    {
        $url = $_SERVER['HTTP_REFERER'];
        $explodeUrl = explode('/', $url);
        $stringId = end($explodeUrl);
        $id = (int) $stringId;
        return $id;
    }

    public function rooms(): array
    {
        return $this->roomRepository->findAll();
    }

    public function setCurrentRoom(): ?Room
    {
        $id = $this->getId();
        $currentRoom = $this->roomRepository->find($id);
        return $currentRoom;
    }

    public function chatMessages(Room $room)
    {
        $messages = $room->getChatMessages();
        return $messages;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            ChatMessageType::class,
            $this->chatMessage
        );
    }

    #[LiveAction]
    public function refreshChatMessages()
    {
        $id = $this->getId();
        $room = $this->roomRepository->find($id);

        return $this->chatMessages($room);
    }
}
