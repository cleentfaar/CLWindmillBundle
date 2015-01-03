<?php

namespace CL\Bundle\WindmillBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cl_windmill');

        $rootNode
            ->children()
                ->scalarNode('default_storage')->defaultValue('orm')->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('board')->defaultValue('CLWindmillBundle:Partial:board.html.twig')->end()
                        ->scalarNode('captures')->defaultValue('CLWindmillBundle:Partial:captures.html.twig')->end()
                        ->scalarNode('clocks')->defaultValue('CLWindmillBundle:Partial:clocks.html.twig')->end()
                        ->scalarNode('game')->defaultValue('CLWindmillBundle:Partial:game.html.twig')->end()
                        ->scalarNode('history')->defaultValue('CLWindmillBundle:Partial:history.html.twig')->end()
                        ->scalarNode('vs')->defaultValue('CLWindmillBundle:Partial:vs.html.twig')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
