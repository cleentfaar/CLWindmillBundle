<?php

namespace CL\Bundle\WindmillBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterStorageAdaptersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $selectedType       = $container->getParameter('cl_windmill.storage.type');
        $registryDefinition = $container->getDefinition('cl_windmill.util.storage_adapter_registry');

        foreach ($container->findTaggedServiceIds('cl_windmill.storage_adapter') as $id => $tags) {
            foreach ($tags as $tagAttributes) {
                if (!isset($tagAttributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('Service "%s" must define the "alias" attribute on "cl_windmill.storage_adapter" tags.', $id));
                }

                if ($tagAttributes['alias'] !== $selectedType) {
                    continue;
                }

                $registryDefinition->addMethodCall('register', array(new Reference($id), $tagAttributes['alias']));
            }
        }
    }
}
