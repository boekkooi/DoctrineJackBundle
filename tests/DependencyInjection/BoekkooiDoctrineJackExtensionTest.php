<?php
namespace Tests\Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection;

use Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\BoekkooiDoctrineJackExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class BoekkooiDoctrineJackExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BoekkooiDoctrineJackExtension
     */
    private $loader;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->loader = new BoekkooiDoctrineJackExtension();
    }


    public function testDefaults()
    {
        $this->loader->load(array('boekkooi_doctrine_jack' => array()), $this->container);

        $this->assertTrue($this->container->hasParameter('boekkooi.doctrine_jack.functions'));
        $this->assertEquals(array('random' => true), $this->container->getParameter('boekkooi.doctrine_jack.functions'));

        $this->assertFalse($this->container->has('boekkooi.doctrine_jack.discriminator_map.listener'));
    }

    /**
     * @dataProvider getFunctions
     */
    public function testLoadAllFunctions($value)
    {
        $this->loader->load(array('boekkooi_doctrine_jack' => array('functions' => $value)), $this->container);

        $this->assertTrue($this->container->hasParameter('boekkooi.doctrine_jack.functions'));
        $this->assertEquals(array('random' => true), $this->container->getParameter('boekkooi.doctrine_jack.functions'));
    }

    public function getFunctions()
    {
        return array(
            array(array('random' => true)),
            array(array()),
            array(true),
        );
    }

    /**
     * @dataProvider getNoFunctions
     */
    public function testLoadNoFunctions($value)
    {
        $this->loader->load(array('boekkooi_doctrine_jack' => array('functions' => $value)), $this->container);

        $this->assertTrue($this->container->hasParameter('boekkooi.doctrine_jack.functions'));
        $this->assertEquals(array(), $this->container->getParameter('boekkooi.doctrine_jack.functions'));
    }

    public function getNoFunctions()
    {
        return array(
            array(false),
            array(null)
        );
    }

    public function testLoadMapping()
    {
        $this->loader->load(array('boekkooi_doctrine_jack' => array(
                'discriminator_map' => array(
                    'SuperEntity1' => array(
                        'one' => 'ChildEntity1',
                        'two' => 'ChildEntity2'
                    ),
                    'SuperEntity2' => array(
                        1 => 'ChildEntity1'
                    ),
                )
            )),
            $this->container
        );

        $this->assertTrue($this->container->has('boekkooi.doctrine_jack.discriminator_map.listener'));
        $this->assertEquals(
            array(
                'SuperEntity1' => array(
                    'one' => 'ChildEntity1',
                    'two' => 'ChildEntity2'
                ),
                'SuperEntity2' => array(
                    1 => 'ChildEntity1'
                ),
            ),
            $this->container->getParameter('boekkooi.doctrine_jack.discriminator_map.mapping')
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected scalar, but got array.
     */
    public function testLoadInvalidMapping()
    {
        $this->loader->load(array('boekkooi_doctrine_jack' => array(
                'discriminator_map' => array(
                    'SuperEntity1' => array(
                        'ChildEntity1' => array('one'),
                    )
                )
            )),
            $this->container
        );
    }
}
