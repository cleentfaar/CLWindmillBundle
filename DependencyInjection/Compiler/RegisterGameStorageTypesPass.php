<?php

namespace CL\Bundle\WindmillBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterGameStorageTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('cl_windmill.storage.game_storage_loader');

        foreach ($container->findTaggedServiceIds('cl_windmill.storage.game_storage') as $id => $factories) {
            foreach ($factories as $factory) {
                if (!isset($factory['alias'])) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "alias" attribute on "cl_windmill.storage.game_storage" tags.', $id));
                }

                $definition->addMethodCall('addStorage', array(new Reference($id), $factory['alias']));
            }
        }
    }
}
