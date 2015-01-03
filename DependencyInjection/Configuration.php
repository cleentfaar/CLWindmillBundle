<?php

namespace CL\Bundle\WindmillBundle\DependencyInjection;

use CL\Bundle\WindmillBundle\Entity\Game;
use CL\Bundle\WindmillBundle\Entity\GameState;
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
                ->scalarNode('move_route')->defaultValue('cl_windmill_game_move')->end()
                ->arrayNode('storage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->defaultValue('orm')->end()
                        ->scalarNode('game_class')->defaultValue(Game::class)->end()
                        ->scalarNode('game_state_class')->defaultValue(GameState::class)->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('board')->defaultValue('CLWindmillBundle:Partial:board.html.twig')->end()
                        ->scalarNode('captures')->defaultValue('CLWindmillBundle:Partial:captures.html.twig')->end()
                        ->scalarNode('clocks')->defaultValue('CLWindmillBundle:Partial:clocks.html.twig')->end()
                        ->scalarNode('game')->defaultValue('CLWindmillBundle:Partial:game.html.twig')->end()
                        ->scalarNode('history')->defaultValue('CLWindmillBundle:Partial:history.html.twig')->end()
                        ->scalarNode('javascripts')->defaultValue('CLWindmillBundle:Partial:javascripts.html.twig')->end()
                        ->scalarNode('hidden_form')->defaultValue('CLWindmillBundle:Partial:hidden_form.html.twig')->end()
                        ->scalarNode('vs')->defaultValue('CLWindmillBundle:Partial:vs.html.twig')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
