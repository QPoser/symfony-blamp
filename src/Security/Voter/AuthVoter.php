<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthVoter extends Voter
{
    protected function supports($attribute, $subject = null)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['AUTH', 'NOT_AUTH']);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case 'AUTH':
                $user = $token->getUser();

                if ($user == 'anon.') {
                    return true;
                }

                return false;
            break;

            case 'NOT_AUTH':
                $user = $token->getUser();

                if ($user == 'anon.') {
                    return false;
                }

                return true;
            break;
        }
    }
}
