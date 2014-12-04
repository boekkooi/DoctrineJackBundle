<?php
namespace Boekkooi\Bundle\DoctrineJackBundle;

use Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\Compiler\DoctrineAnyPass;
use Boekkooi\Bundle\DoctrineJackBundle\DependencyInjection\Compiler\DoctrineFunctionPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class BoekkooiDoctrineJackBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DoctrineFunctionPass());
        $container->addCompilerPass(new DoctrineAnyPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
