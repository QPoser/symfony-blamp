<?php

namespace App\Security\Voter;

use App\Entity\Company\Coupon;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CouponVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ACCEPT'])
            && $subject instanceof Coupon;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Coupon $subject */
        switch ($attribute) {
            case 'ACCEPT':
                if ($subject->getCompany()->getBusinessUsers()->contains($user) || true) { return true; }
                break;
        }

        return false;
    }
}
