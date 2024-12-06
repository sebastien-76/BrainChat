<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW], true)
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        // Vérifie si l'utilisateur est connecté
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        /** @var User $subject */
        switch ($attribute) {
            case self::EDIT:
                // Autorise seulement si l'utilisateur modifie son propre profil
                return $subject->getId() === $currentUser->getId();

            case self::VIEW:
                // Permet de voir tous les utilisateurs, ou seulement lui-même
                return $subject->getId() === $currentUser->getId()
                    || in_array('ROLE_ADMIN', $currentUser->getRoles(), true);
        }

        return false;
    }
}

