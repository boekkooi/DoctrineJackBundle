<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class BoekkooiDoctrineJackExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->loadFunctions($container, $config);
        $this->loadDiscriminatorMap($container, $loader, $config);
    }

    private function loadDiscriminatorMap(ContainerBuilder $container, LoaderInterface $loader, array $config)
    {
        if (empty($config['discriminator_map'])) {
            return;
        }
        $loader->load('discriminator_map.yml');
        $container->setParameter('boekkooi.doctrine_jack.discriminator_map.mapping', $config['discriminator_map']);
    }

    private function loadFunctions(ContainerBuilder $container, array $config)
    {
        $container->setParameter('boekkooi.doctrine_jack.functions', array_filter($config['functions']));
    }
} 