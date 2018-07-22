<?php

namespace App\Repository;

use App\Entity\ReviewComment;

use App\Services\TreeEntityManager;
use Doctrine\ORM\EntityManager;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class ReviewCommentRepository extends NestedTreeRepository
{
    public function __construct(EntityManager $manager)
    {
        $entityClass = ReviewComment::class;
        $TEM = new TreeEntityManager();
        $manager = $TEM->getTEM($manager);

        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }
}