<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.07.18
 * Time: 16:51
 */

namespace App\Services;


use App\Entity\Category\Category;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CategoryService
{
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var TokenStorage
     */
    private $storage;

    /**
     * CompanyService constructor.
     * @param EntityManager $manager
     * @param Container $container
     * @param TokenStorage $storage
     */
    public function __construct(EntityManager $manager, Container $container, TokenStorage $storage)
    {
        $this->manager = $manager;
        $this->container = $container;
        $this->storage = $storage;
    }

    public function create(Category $category)
    {
        if ($category->getParentCategory() == null) {
            $category->setLevel(0);
        } else {
            $category->setLevel($category->getParentCategory()->getLevel() + 1);
        }
        $category->generatePath();
        $this->manager->persist($category);
        $this->manager->flush();
    }

    public function edit(Category $category)
    {

        $this->manager->flush();
    }

    public function delete(Category $category)
    {
        $this->manager->remove($category);
        $this->manager->flush();
    }

    public function flusher()
    {
        $this->manager->flush();
    }
}