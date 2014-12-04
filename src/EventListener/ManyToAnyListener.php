<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\EventListener;

use Boekkooi\Bundle\DoctrineJackBundle\Mapping\ClassMetadata;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class ManyToAnyListener implements EventSubscriber
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
//            Events::loadClassMetadata,
//            Events::postFlush,
            ToolEvents::postGenerateSchemaTable
        );
    }

    public function postGenerateSchemaTable(\Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs $event)
    {
        $class = $event->getClassMetadata();
        if (!$class instanceof ClassMetadata) {
            return;
        }

        foreach ($class->associationMappings as $mapping) {
            if (isset($mapping['inherited'])) {
                continue;
            }

            $foreignClass = $this->em->getClassMetadata($mapping['targetEntity']);

            if ($mapping['type'] === ClassMetadata::ONE_TO_ANY) {
                //
            } elseif ($mapping['type'] === ClassMetadata::MANY_TO_ANY) {
                //
            }
        }

        $table = $event->getClassTable();
        $table->addColumn('related_class', 'string', array('nullable' => false, 'length' => '150'));
        $table->addColumn('related_id', 'string', array('nullable' => false, 'length' => '100'));
//        $table->addUniqueIndex(array('related_class', 'related_id'));
    }
}
