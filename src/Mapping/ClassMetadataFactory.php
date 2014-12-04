<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Mapping;

use Doctrine\ORM\EntityManager;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class ClassMetadataFactory extends \Doctrine\ORM\Mapping\ClassMetadataFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setEntityManager(EntityManager $em)
    {
        parent::setEntityManager($em);
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function newClassMetadataInstance($className)
    {
        return new ClassMetadata($className, $this->em->getConfiguration()->getNamingStrategy());
    }
}
