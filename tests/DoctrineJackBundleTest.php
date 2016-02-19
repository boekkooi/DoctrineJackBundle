<?php
namespace Tests\Boekkooi\Bundle\DoctrineJackBundle;

use Boekkooi\Bundle\DoctrineJackBundle\BoekkooiDoctrineJackBundle;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class DoctrineJackBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $bundle = new BoekkooiDoctrineJackBundle();
        $this->assertEquals('BoekkooiDoctrineJackBundle', $bundle->getName());
    }
}
