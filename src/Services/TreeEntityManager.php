<?php

namespace App\Services;


use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\DoctrineExtensions;
use Gedmo\Timestampable\TimestampableListener;
use Gedmo\Tree\TreeListener;


class TreeEntityManager
{
    /**
     * @param EntityManager $manager
     * @return EntityManager
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\ORMException
     */
    public function getTEM(EntityManager $manager)
    {
        $config = $manager->getConfiguration();
        $evm = $manager->getEventManager();
        $connection = $manager->getConnection();

        $cache = new ArrayCache();
// standard annotation reader
        $annotationReader = new AnnotationReader();//AnnotationReader();
        $cachedAnnotationReader = new CachedReader(
            $annotationReader, // use reader
            $cache // and a cache driver
        );
// create a driver chain for metadata reading
        $driverChain = new MappingDriverChain();
// load superclass metadata mapping only, into driver chain
// also registers Gedmo annotations.NOTE: you can personalize it
        DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
            $driverChain, // our metadata driver chain, to hook into
            $cachedAnnotationReader // our cached annotation reader
        );
// now we want to register our application entities,
// for that we need another metadata driver used for Entity namespace
        $annotationDriver = new AnnotationDriver(
            $cachedAnnotationReader, // our cached annotation reader
            array(__DIR__ . './../../src/Entity') // paths to look in
        );
// NOTE: driver for application Entity can be different, Yaml, Xml or whatever
// register annotation driver for our application Entity namespace
        $driverChain->addDriver($annotationDriver, 'App\Entity');
// general ORM configuration

//        $config->setProxyDir(sys_get_temp_dir());
//        $config->setProxyNamespace('Proxy');
//        $config->setAutoGenerateProxyClasses(false); // this can be based on production config.
// register metadata driver
        $config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
// Third, create event manager and hook prefered extension listeners

// gedmo extension listeners
// tree
        $treeListener = new TreeListener();
        $treeListener->setAnnotationReader($cachedAnnotationReader);
        $evm->addEventSubscriber($treeListener);
// loggable, not used in example
//$loggableListener = new Gedmo\Loggable\LoggableListener;
//$loggableListener->setAnnotationReader($cachedAnnotationReader);
//$loggableListener->setUsername('admin');
//$evm->addEventSubscriber($loggableListener);
// timestampable
        $timestampableListener = new TimestampableListener();
        $timestampableListener->setAnnotationReader($cachedAnnotationReader);
        $evm->addEventSubscriber($timestampableListener);
// sortable, not used in example
//$sortableListener = new Gedmo\Sortable\SortableListener;
//$sortableListener->setAnnotationReader($cachedAnnotationReader);
//$evm->addEventSubscriber($sortableListener);
// mysql set names UTF-8 if required

// Finally, create entity manager

        return EntityManager::create($connection, $config, $evm);
    }
}