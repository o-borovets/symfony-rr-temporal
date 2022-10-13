<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\DependencyInjection\CompilerPass;

use RoadRunnerTemporalSymfony\ActivityFinalizer\DelegateFinalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActivityFinalizerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(DelegateFinalizer::class)) {
            return;
        }

        $delegateFinalizerDefinition = $container->getDefinition(DelegateFinalizer::class);

        $taggedServices = $container->findTaggedServiceIds('temporal_symfony.finalizer');

        foreach ($taggedServices as $id => $tags) {
            $delegateFinalizerDefinition->addMethodCall('addFinalizer', [new Reference($id)]);
        }
    }
}
