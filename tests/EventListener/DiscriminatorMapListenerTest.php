<?php
namespace Tests\Boekkooi\Bundle\DoctrineJackBundle\EventListener;

use Boekkooi\Bundle\DoctrineJackBundle\EventListener\DiscriminatorMapListener;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class DiscriminatorMapListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadClassMetadata()
    {
        $map = array(
            'TestEntity' => array(
                'test' => 'TestEntity',
                'test1' => 'TestChildEntity'
            )
        );

        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $classMetadata = $this->getMockBuilder('Doctrine\\ORM\\Mapping\\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->getMock();
        $classMetadata
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('TestEntity');
        $classMetadata
            ->expects($this->once())
            ->method('setDiscriminatorMap')
            ->with($map['TestEntity']);

        $listener = new DiscriminatorMapListener($map);
        $listener->loadClassMetadata(
            new LoadClassMetadataEventArgs($classMetadata, $objectManager)
        );
    }

    public function testCleanMaps()
    {
        $dirty = array(
            'TestEntity' => array(
                'test' => '\\TestEntity'
            ),
            '\\TestEntity' => array(
                'test1' => '\\Test\\Entity1',
                'test2' => '\\Test\\Entity2'
            )
        );
        $clean = array(
            'TestEntity' => array(
                'test' => 'TestEntity',
                'test1' => 'Test\\Entity1',
                'test2' => 'Test\\Entity2'
            )
        );

        $listener = new DiscriminatorMapListener($dirty);
        $this->assertEquals($clean, $listener->getEntityMaps());

        $dirty = array(
            'TestEntity' => array(
                'test' => '\\TestEntity'
            ),
            '\\TestEntity' => array(
                'test' => '\\Test\\Entity'
            )
        );
        $clean = array(
            'TestEntity' => array(
                'test' => 'Test\\Entity',
            )
        );
        $listener = new DiscriminatorMapListener($dirty);
        $this->assertEquals($clean, $listener->getEntityMaps());
    }

    /**
     * @dataProvider getNoMaps
     */
    public function testLoadClassMetadataSkip(array $map, \PHPUnit_Framework_MockObject_MockObject $classMetadata)
    {
        $objectManager = $this->getMock('Doctrine\Common\Persistence\ObjectManager');

        $classMetadata->expects($this->never())->method('setDiscriminatorMap');

        $listener = new DiscriminatorMapListener($map);
        $listener->loadClassMetadata(new LoadClassMetadataEventArgs($classMetadata, $objectManager));
    }

    public function getNoMaps()
    {
        return array(
            array(
                array(),
                $this->getMock('Doctrine\\ORM\\Mapping\\ClassMetadataInfo', null, array('NoTestEntity'))
            ),
            array(
                array(),
                $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
            ),
            array(
                array(
                    'AEntity' => array()
                ),
                $this->getMock('Doctrine\\ORM\\Mapping\\ClassMetadataInfo', null, array('TestEntity'))
            )
        );
    }
}