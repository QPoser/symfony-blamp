<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 22.07.18
 * Time: 23:52
 */

namespace App\Services;


use App\Entity\Company\Company;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class EventService
{
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function addEventByUser(User $user, string $message, User $sender)
    {
        if ($user->getId() == $sender->getId()) {
            return false;
        }
        $event = new Event();
        $event->setUser($user);
        $event->setEventMessage($message);
        $event->setIsCompanySender(false);
        $event->setIsSeen(false);
        $event->setSenderUser($sender);

        $this->manager->persist($event);
        $this->manager->flush();

        return true;
    }

    public function addEventByCompany(User $user, string $message, Company $company)
    {

    }

}