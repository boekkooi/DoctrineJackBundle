<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Extension\AnyToOne\Driver;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Gedmo\Mapping\Driver\AbstractAnnotationDriver;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class Annotation extends AbstractAnnotationDriver
{
    /**
     * Annotation any to one class
     */
    const ANY_TO_ONE = 'Boekkooi\\Bundle\\DoctrineJackBundle\\Mapping\\AnyToOne';

    /**
     * {@inheritDoc}
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        /** @var ClassMetadataInfo $meta */
        $class = $meta->getReflectionClass();

        foreach ($class->getProperties() as $property) {
            if ($meta->isMappedSuperclass && !$property->isPrivate() ||
                $meta->isInheritedField($property->name) ||
                isset($meta->associationMappings[$property->name]['inherited'])
            ) {
                continue;
            }

            /** @var \Boekkooi\Bundle\DoctrineJackBundle\Mapping\Annotation\AnyToOne $anyToOne */
            $anyToOne = $this->reader->getPropertyAnnotation($property, self::ANY_TO_ONE);
            if ($anyToOne === null) {
                continue;
            }

            $config['hasAnyToOne'] = true;
            $config['fields'][] = array (
                'fieldName' => $property->getName(),
                'unique' => $anyToOne->unique,
                'storageStrategy' => $anyToOne->storage,
                'fetchStrategy' => $anyToOne->fetch
            );
        }
    }
}