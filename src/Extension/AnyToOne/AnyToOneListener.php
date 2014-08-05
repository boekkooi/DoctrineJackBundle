<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\Extension\AnyToOne;

use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Gedmo\Mapping\MappedEventSubscriber;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class AnyToOneListener extends MappedEventSubscriber
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
            ToolEvents::postGenerateSchema
        );
    }

    /**
     * Maps additional metadata
     *
     * @param LoadClassMetadataEventArgs $event
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $this->loadMetadataForObjectClass($event->getObjectManager(), $event->getClassMetadata());
    }

    /**
     * Generates additional columns
     *
     * @param GenerateSchemaEventArgs $event
     * @return void
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $event)
    {
        $objectManager = $event->getEntityManager();
        $platform = $objectManager->getConnection()->getDatabasePlatform();
        $quoteStrategy = $objectManager->getConfiguration()->getQuoteStrategy();

        $allMetadata = $objectManager->getMetadataFactory()->getAllMetadata();

        foreach ($allMetadata as $metadata) {
            if (!$metadata instanceof ClassMetadataInfo) {
                continue;
            }

            $config = $this->getConfiguration($objectManager, $metadata->getName());
            if (!isset($config['hasAnyToOne']) || !$config['hasAnyToOne']) {
                continue;
            }

            $table = $event->getSchema()->getTable($metadata->getTableName());
            foreach ($config['fields'] as $mapping) {
                $this->generateAnyToOneSchema(
                    $quoteStrategy,
                    $platform,
                    $metadata,
                    $table,
                    $mapping
                );
            }
        }
    }

    protected function generateAnyToOneSchema(QuoteStrategy $quoteStrategy, AbstractPlatform $platform, ClassMetadataInfo $metadata, Table $table, array $mapping)
    {
        // Add the
        $columnName = $quoteStrategy->getColumnName($mapping['fieldName'], $metadata, $platform);
        $columnType = 'string';
        $table->addColumn($columnName, $columnType, $this->generateColumnSchemaOptions($metadata, $columnType, $mapping));

        $columnName = $quoteStrategy->getColumnName($mapping['fieldName'], $metadata, $platform);
        $columnType = 'string';


        $table->addColumn($columnName, $columnType, $this->generateColumnSchemaOptions($metadata, $columnType, $mapping));

        if (isset($mapping['unique']) && $mapping['unique']) {
            $table->addUniqueIndex(
                array($mapping['columnName'], $mapping['mapColumnName'])
            );
        } else {
            $table->addIndex(
                array($mapping['columnName'], $mapping['mapColumnName'])
            );
        }
    }

    protected function generateColumnSchemaOptions(ClassMetadataInfo $metadata, $columnType, array $mapping)
    {
        $options = array();
        $options['length'] = isset($mapping['length']) ? $mapping['length'] : null;
        $options['notnull'] = isset($mapping['nullable']) ? ! $mapping['nullable'] : true;
        if ($metadata->isInheritanceTypeSingleTable() && count($metadata->parentClasses) > 0) {
            $options['notnull'] = false;
        }

        if (strtolower($columnType) == 'string' && $options['length'] === null) {
            $options['length'] = 255;
        }

        if (isset($mapping['precision'])) {
            $options['precision'] = $mapping['precision'];
        }

        if (isset($mapping['scale'])) {
            $options['scale'] = $mapping['scale'];
        }

        if (isset($mapping['default'])) {
            $options['default'] = $mapping['default'];
        }

        if (isset($mapping['columnDefinition'])) {
            $options['columnDefinition'] = $mapping['columnDefinition'];
        }

        if (isset($mapping['options'])) {
            $knownOptions = array('comment', 'unsigned', 'fixed', 'default');

            foreach ($knownOptions as $knownOption) {
                if ( isset($mapping['options'][$knownOption])) {
                    $options[$knownOption] = $mapping['options'][$knownOption];

                    unset($mapping['options'][$knownOption]);
                }
            }

            $options['customSchemaOptions'] = $mapping['options'];
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    protected function getNamespace()
    {
        return __NAMESPACE__;
    }
}