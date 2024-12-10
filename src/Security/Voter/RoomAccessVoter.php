<?php
namespace App\Security\Voter;

use App\Entity\Room;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RoomAccessVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === 'VIEW' && $subject instanceof Room;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Room $room */
        $room = $subject;

        // Si la room n'est pas privée, accès autorisé
        if (!$room->getIsPrivate()) {
            return true;
        }

        // Vérifier si l'utilisateur est participant
        return $room->getParticipants()->exists(
            fn($key, $participant) => $participant->getUser()->getId() === $user->getId()
        );
    }
}