<?php

namespace CL\Bundle\WindmillBundle;

use CL\Bundle\WindmillBundle\DependencyInjection\Compiler\RegisterGameStorageTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CLWindmillBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterGameStorageTypesPass());
    }
}
