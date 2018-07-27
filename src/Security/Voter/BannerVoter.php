<?php

namespace App\Security\Voter;

use App\Entity\Advert\AdvertDescription;
use App\Entity\Advert\Banner;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BannerVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['VERIFY', 'PAY'])
            && $subject instanceof Banner;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'VERIFY':
                if ($user->isAdmin()) { return true; }
                break;
            case 'PAY':
                if ($user->isAdmin() || $subject->getUser()->getId() == $user->getId()) { return true; }
                break;
        }

        return false;
    }
}
