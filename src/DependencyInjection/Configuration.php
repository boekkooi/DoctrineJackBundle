<?php
namespace Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('boekkooi_doctrine_jack');

        $rootNode->append($this->getDiscriminatorMapNode());
        $rootNode->append($this->getFunctionsNode());

        return $treeBuilder;
    }

    protected function getDiscriminatorMapNode()
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root('discriminator_map');
        $node
            ->treatNullLike(array())
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->useAttributeAsKey('name')
                ->prototype('scalar')->end()
            ->end();

        return $node;
    }

    protected function getFunctionsNode()
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root('functions');
        $node
            ->addDefaultsIfNotSet()
            ->treatTrueLike(array('random' => true))
            ->treatFalseLike(array('random' => false))
            ->treatNullLike(array('random' => false))
            ->children()
                ->booleanNode('random')->defaultTrue()->end()
            ->end();

        return $node;
    }
}
