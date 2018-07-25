<?php

namespace App\Security\Voter;

use App\Entity\Company\Company;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'DELETE', 'VERIFY'])
            && $subject instanceof Company;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        /** @var Company $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'EDIT':
                if ($subject->getBusinessUsers()->contains($user) || $user->isAdmin()) { return true; }
                break;
            case 'DELETE':
                if ($user->isAdmin()) { return true; }
                break;
            case 'VERIFY':
                if ($user->isAdmin()) { return true; }
                break;
        }

        return false;
    }
}
