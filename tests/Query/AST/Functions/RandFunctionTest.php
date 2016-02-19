<?php
namespace Tests\Boekkooi\Bundle\DoctrineJackBundle\Query\AST\Functions;

use Boekkooi\Bundle\DoctrineJackBundle\Query\AST\Functions\RandFunction;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class RandFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RandFunction
     */
    private $function;

    protected function setUp()
    {
        $this->function = new RandFunction('RAND');
    }

    public function testParse()
    {
        $configuration = $this->getMockBuilder('Doctrine\ORM\Configuration')->disableOriginalConstructor()->getMock();
        $configuration->expects($this->atLeastOnce())->method('getCustomNumericFunction')
            ->with('rand')
            ->willReturn('Boekkooi\Bundle\DoctrineJackBundle\Query\AST\Functions\RandFunction');

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->atLeastOnce())->method('getConfiguration')
            ->with()
            ->willReturn($configuration);

        $query = new Query($em);
        $query->setDQL('SELECT RAND() AS x FROM stdClass AS y');

        $parser = new Parser($query);
        $parser->getAST();
    }

    /**
     * @dataProvider getPlatforms
     */
    public function testGetSql($platform, $expected)
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();
        $connection->expects($this->once())
            ->method('getDatabasePlatform')
            ->with()
            ->willReturn($platform);

        $walker = $this->getMockBuilder('Doctrine\\ORM\\Query\\SqlWalker')->disableOriginalConstructor()->getMock();
        $walker->expects($this->once())
            ->method('getConnection')
            ->with()
            ->willReturn($connection);

        $this->assertEquals($expected, $this->function->getSql($walker));
    }

    public function getPlatforms()
    {
        return array(
            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\SQLServer2005Platform'),
                'RAND()'
            ),
            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\SQLServer2008Platform'),
                'RAND()'
            ),
            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\SQLServer2012Platform'),
                'RAND()'
            ),
            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\SQLAzurePlatform'),
                'RAND()'
            ),
            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\MySqlPlatform'),
                'RAND()'
            ),

            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\SqlitePlatform'),
                '((random() / 18446744073709551616) + 0.5)'
            ),

            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\PostgreSqlPlatform'),
                'RANDOM()'
            ),

            array(
                $this->getMock('Doctrine\\DBAL\\Platforms\\OraclePlatform'),
                'DBMS_RANDOM.VALUE'
            )
        );
    }


    /**
     * @dataProvider getUnsupportedPlatforms
     * @expectedException \Doctrine\ORM\ORMException
     */
    public function testUnsupportedGetSql($platform)
    {
        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')->disableOriginalConstructor()->getMock();
        $connection->expects($this->once())
            ->method('getDatabasePlatform')
            ->with()
            ->willReturn($platform);

        $walker = $this->getMockBuilder('Doctrine\\ORM\\Query\\SqlWalker')->disableOriginalConstructor()->getMock();
        $walker->expects($this->once())
            ->method('getConnection')
            ->with()
            ->willReturn($connection);

        $this->function->getSql($walker);
    }

    public function getUnsupportedPlatforms()
    {
        return array(
            array($this->getMock('Doctrine\\DBAL\\Platforms\\DB2Platform')),
            array($this->getMock('Doctrine\\DBAL\\Platforms\\DrizzlePlatform')),
            array($this->getMock('Doctrine\\DBAL\\Platforms\\SQLServerPlatform')),
        );
    }
}
