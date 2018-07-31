<?php
/**
 * Created by PhpStorm.
 * User: qposer
 * Date: 31.07.18
 * Time: 19:40
 */

namespace App\Services;


use App\Entity\Company\Protector;
use Doctrine\ORM\EntityManager;

class ProtectorService
{

    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function create(Protector $protector)
    {
        $this->manager->persist($protector);
        $this->manager->flush();
    }

    public function remove(Protector $protector)
    {
        $this->manager->remove($protector);
        $this->manager->flush();
    }

}