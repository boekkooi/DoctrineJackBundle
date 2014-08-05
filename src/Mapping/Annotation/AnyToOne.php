<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Mapping\Annotation;

use Doctrine\ORM\Mapping\Annotation;

class AnyToOne implements Annotation
{
    /**
     * The storage strategy to use for the association.
     *
     * @var string
     *
     * @Enum({"SPLIT", "COMBINE"})
     */
    public $storage = 'SPLIT';

    /**
     * @var boolean
     */
    public $unique = false;

    /**
     * @var boolean
     */
    public $nullable = false;

    /**
     * The fetching strategy to use for the association.
     *
     * @var string
     *
     * @Enum({"LAZY", "EAGER", "EXTRA_LAZY"})
     */
    public $fetch = 'LAZY';

//    /**
//     * @var \Doctrine\ORM\Mapping\Column
//     */
//    public $column = null;
//
//    /**
//     * @var \Doctrine\ORM\Mapping\Column
//     */
//    public $typeColumn = null;
}
