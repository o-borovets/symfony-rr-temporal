<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\DependencyInjection;

use RoadRunnerTemporalSymfony\ActivityFinalizer\FinalizerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Temporal\Activity\ActivityInterface;
use Temporal\Workflow\WorkflowInterface;

class TemporalSymfonyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.php');

        $container->setParameter('symfony_roadrunner_temporal.address', $config['address']);

        $container->registerAttributeForAutoconfiguration(
            WorkflowInterface::class,
            static function (ChildDefinition $definition) {
                $definition->addTag('temporal_symfony.workflow');
            }
        );

        $container->registerAttributeForAutoconfiguration(
            ActivityInterface::class,
            static function (ChildDefinition $definition) {
                $definition->addTag('temporal_symfony.activity');
            }
        );

        $container
            ->registerForAutoconfiguration(FinalizerInterface::class)
            ->addTag('temporal_symfony.finalizer')
        ;
    }
}
