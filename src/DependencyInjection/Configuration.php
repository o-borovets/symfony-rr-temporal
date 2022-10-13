<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('symfony_roadrunner_temporal');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('address')->defaultValue('127.0.0.1:7233')
        ;

        return $treeBuilder;
    }
}
