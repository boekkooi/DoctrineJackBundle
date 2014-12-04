<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class DoctrineAnyPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('doctrine.entity_managers')/* || !$container->hasParameter('boekkooi.doctrine_jack.functions')*/) {
            return;
        }

        $managers = $container->getParameter('doctrine.entity_managers');
        foreach ($managers as $name => $service) {
            // Customize configuration with custom metadata factory
            $ormConfigDef = $container->getDefinition(sprintf('doctrine.orm.%s_configuration', $name));
            $ormConfigDef->addMethodCall('setClassMetadataFactoryName', array('Boekkooi\\Bundle\\DoctrineJackBundle\\Mapping\\ClassMetadataFactory'));

            // Replace default annotation driver
            $chainDriverDef = $container->getDefinition(sprintf('doctrine.orm.%s_metadata_driver', $name));
            foreach ($chainDriverDef->getMethodCalls() as $call) {
                if (count($call) !== 2 || $call[0] !== 'addDriver' || !is_array($call[1]) || !isset($call[1][0]) || !$call[1][0] instanceof Definition) {
                    continue;
                }
                /** @var Definition $driverDef */
                $driverDef = $call[1][0];
                if ($driverDef->getClass() !== 'Doctrine\\ORM\\Mapping\\Driver\\AnnotationDriver') {
                    continue;
                }
                $driverDef->setClass('Boekkooi\\Bundle\\DoctrineJackBundle\\Mapping\\Driver\\AnnotationDriver');
            }
        }
    }
}
