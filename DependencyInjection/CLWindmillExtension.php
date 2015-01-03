<?php

namespace CL\Bundle\WindmillBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CLWindmillExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->setParameters($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param string[]         $config
     */
    protected function setParameters(ContainerBuilder $container, array $config)
    {
        $intactArrays = ['templates'];

        foreach ($config as $key => $value) {
            if (is_array($value) && !in_array($key, $intactArrays)) {
                foreach ($value as $k => $v) {
                    $container->setParameter(sprintf('cl_windmill.%s.%s', $key, $k), $v);
                }
            } else {
                $container->setParameter(sprintf('cl_windmill.%s', $key), $value);
            }
        }
    }
}
