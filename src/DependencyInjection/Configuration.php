<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Temporal\Worker\WorkerFactoryInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('temporal_symfony');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('address')
            ->defaultValue('127.0.0.1:7233')
            ->end()
            ->scalarNode('namespace')
            ->defaultValue('default')
            ->end()
            ->scalarNode('defaultWorker')
            ->defaultValue(WorkerFactoryInterface::DEFAULT_TASK_QUEUE)
            ->end()
            ->arrayNode('workers')
            ->scalarPrototype()
            ->defaultValue(WorkerFactoryInterface::DEFAULT_TASK_QUEUE)
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
