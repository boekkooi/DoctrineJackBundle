<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Mapping;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class ClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata
{
    /**
     * Identifies a one-to-any association.
     */
//    const ONE_TO_ANY = 16;
    const ONE_TO_ANY = 17;

    /**
     * Identifies a many-to-any association.
     */
    const MANY_TO_ANY = 34;

    public function mapOneToAny(array $mapping)
    {
        $mapping['type'] = self::ONE_TO_ANY;
        $mapping['isOwningSide'] = true;

        $mapping = $this->_validateAndCompleteOneToOneMapping($mapping);
        $this->_storeAssociationMapping($mapping);
    }

    public function mapManyToAny(array $mapping)
    {
        $mapping['type'] = self::MANY_TO_ANY;
        $mapping['isOwningSide'] = true;

        $mapping = $this->_validateAndCompleteOneToOneMapping($mapping);
        var_dump($mapping);die;
        $this->_storeAssociationMapping($mapping);
    }
}
