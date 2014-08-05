<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class DoctrineFunctionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('doctrine.entity_managers') || !$container->hasParameter('boekkooi.doctrine_jack.functions')) {
            return;
        }

        $functions = $container->getParameter('boekkooi.doctrine_jack.functions');
        if (empty($functions)) {
            return;
        }

        $managers = $container->getParameter('doctrine.entity_managers');
        foreach ($managers as $name => $service) {
            $ormConfigDef = $container->getDefinition(sprintf('doctrine.orm.%s_configuration', $name));

            if (isset($functions['random']) && $functions['random']) {
                $ormConfigDef->addMethodCall('addCustomNumericFunction', array('RAND', 'Boekkooi\Bundle\DoctrineJackBundle\Query\AST\Functions\RandFunction'));
            }
        }
    }
}