<?php

namespace App\Security\Voter;

use App\Entity\Review\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReviewVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'VIEW', 'VERIFY', 'DELETE'])
            && $subject instanceof Review;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Review $subject */
        /** @var User $user */
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
                if ($subject->getUser() == $user || $user->isAdmin()) {
                    return true;
                }
                break;
            case 'VIEW':
                if ($subject->isActive() || $subject->getUser()->getId() == $user->getId() || $user->isAdmin()) { return true; }
                break;
            case 'VERIFY':
                if ($user->isAdmin()) {
                    return true;
                }
                break;
            case 'DELETE':
                if ($user->isAdmin() || $subject->getUser()->getId() == $user->getId()) { return true; }
                break;
        }

        return false;
    }
}
