<?php
namespace Tests\Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\Compiler;

use Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\Compiler\DoctrineFunctionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class DoctrineFunctionPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineFunctionPass
     */
    private $compiler;

    protected function setUp()
    {
        $this->compiler = new DoctrineFunctionPass();
    }

    public function testFunctions()
    {
        /** @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject $container */
        $def = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $def->expects($this->once())
            ->method('addMethodCall')
            ->with('addCustomNumericFunction', array('RAND', 'Boekkooi\Bundle\DoctrineJackBundle\Query\AST\Functions\RandFunction'));

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder', array('getDefinition'));
        $container->expects($this->once())
            ->method('getDefinition')
            ->with('doctrine.orm.default_configuration')->willReturn($def);

        $container->setParameter('doctrine.entity_managers', array('default' => array()));
        $container->setParameter('boekkooi.doctrine_jack.functions', array('random' => true));

        $this->compiler->process($container);
    }

    public function testDisabledFunctions()
    {
        /** @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject $container */
        $def = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $def->expects($this->never())->method('addMethodCall')->withAnyParameters();

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder', array('getDefinition'));
        $container->expects($this->once())
            ->method('getDefinition')
            ->with('doctrine.orm.default_configuration')
            ->willReturn($def);

        $container->setParameter('doctrine.entity_managers', array('default' => array()));
        $container->setParameter('boekkooi.doctrine_jack.functions', array('random' => false));

        $this->compiler->process($container);
    }

    public function testSkipFunctions()
    {
        // Skip/returns
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder', array('getDefinition'));
        $container->expects($this->never())->method('getDefinition')->withAnyParameters();

        $this->compiler->process($container);

        $container->setParameter('doctrine.entity_managers', array());
        $this->compiler->process($container);

        $container->setParameter('boekkooi.doctrine_jack.functions', array());
        $this->compiler->process($container);

        $container->setParameter('boekkooi.doctrine_jack.functions', array('random' => true));
        $this->compiler->process($container);
    }
}
