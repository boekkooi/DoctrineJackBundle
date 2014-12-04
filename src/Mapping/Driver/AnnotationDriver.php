<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Mapping\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class AnnotationDriver extends \Doctrine\ORM\Mapping\Driver\AnnotationDriver
{
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        parent::loadMetadataForClass($className, $metadata);

        /* @var $metadata \Boekkooi\Bundle\DoctrineJackBundle\Mapping\ClassMetadata */
        $class = $metadata->getReflectionClass();
        if (!$class) {
            // this happens when running annotation driver in combination with
            // static reflection services. This is not the nicest fix
            $class = new \ReflectionClass($metadata->name);
        }

        // Evaluate annotations on properties/fields
        foreach ($class->getProperties() as $property) {
            if ($metadata->isMappedSuperclass && !$property->isPrivate()
                ||
                $metadata->isInheritedField($property->name)
                ||
                $metadata->isInheritedAssociation($property->name)
            ) {
                continue;
            }

            $mapping = array();
            $mapping['fieldName'] = $property->getName();

            if ($oneToAnyAnnot = $this->reader->getPropertyAnnotation($property, 'Boekkooi\Bundle\DoctrineJackBundle\Mapping\OneToAny')) {
                /** @var \Boekkooi\Bundle\DoctrineJackBundle\Mapping\OneToAny $oneToAnyAnnot */
                $mapping['targetEntity'] = $oneToAnyAnnot->targetEntity;
                $mapping['inversedBy'] = $oneToAnyAnnot->inversedBy;
                $mapping['cascade'] = $oneToAnyAnnot->cascade;
                $metadata->mapOneToAny($mapping);
            } elseif ($manyToAnyAnnot = $this->reader->getPropertyAnnotation($property, 'Boekkooi\Bundle\DoctrineJackBundle\Mapping\ManyToAny')) {
                /** @var \Boekkooi\Bundle\DoctrineJackBundle\Mapping\ManyToAny $manyToAnyAnnot */
                $mapping['targetEntity'] = $manyToAnyAnnot->targetEntity;
                $mapping['inversedBy'] = $manyToAnyAnnot->inversedBy;
                $mapping['cascade'] = $manyToAnyAnnot->cascade;
                $metadata->mapManyToAny($mapping);
            }
        }
    }


    /**
     * We need to override since the default uses `self` not `static`
     * {@inheritdoc}
     */
    static public function create($paths = array(), AnnotationReader $reader = null)
    {
        if ($reader == null) {
            $reader = new AnnotationReader();
        }

        return new static($reader, $paths);
    }
}
