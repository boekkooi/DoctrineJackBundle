<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class DiscriminatorMapListener implements EventSubscriber
{
    /**
     * @var array
     */
    private $entityMaps = array();

    public function __construct(array $entityMaps)
    {
        $this->mergeEntityMaps($entityMaps);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata
        );
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        if (empty($this->entityMaps)) {
            return;
        }

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        $metadata = $event->getClassMetadata();
        if (!method_exists($metadata, 'setDiscriminatorMap')) {
            return;
        }

        if (!array_key_exists($metadata->getName(), $this->entityMaps)) {
            return;
        }

        $metadata->setDiscriminatorMap($this->entityMaps[$metadata->getName()]);
    }

    public function getEntityMaps()
    {
        return $this->entityMaps;
    }

    protected function mergeEntityMaps(array $entityMaps)
    {
        foreach ($entityMaps as $className => $map) {
            $className = ltrim($className, '\\');
            if (!isset($this->entityMaps[$className])) {
                $this->entityMaps[$className] = array();
            }

            foreach ($map as $name => $cls) {
                $this->entityMaps[$className][$name] = ltrim($cls, '\\');
            }
        }
    }
}
