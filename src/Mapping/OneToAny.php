<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class OneToAny extends Annotation
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var array<string>
     */
    public $cascade;

    /**
     * @var string
     */
    public $inversedBy;
}
