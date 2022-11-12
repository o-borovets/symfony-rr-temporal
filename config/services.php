<?php

declare(strict_types=1);

use RoadRunnerTemporalSymfony\TemporalWorkerRunner;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Worker\WorkerFactoryInterface;
use Temporal\WorkerFactory;

return static function (ContainerConfigurator $container) {
    // $parameters = $container->parameters();

    $services = $container->services();

    $services
        ->set(WorkerFactoryInterface::class)
        ->factory([WorkerFactory::class, 'create'])
    ;

    $services
        ->set(\RoadRunnerTemporalSymfony\TemporalWorkerRunnerInterface::class, \RoadRunnerTemporalSymfony\TemporalWorkerRunner::class)
        ->public()
        ->args([
            service(WorkerFactoryInterface::class),
            tagged_locator('temporal_symfony.activity'),
            tagged_locator('temporal_symfony.workflow'),
            service(\RoadRunnerTemporalSymfony\ActivityFinalizer\FinalizerInterface::class)
        ])
    ;

    $services
        ->set(\RoadRunnerTemporalSymfony\ActivityFinalizer\FinalizerInterface::class, \RoadRunnerTemporalSymfony\ActivityFinalizer\DelegateFinalizer::class)
        ->args([
            tagged_iterator('temporal_symfony.finalizer'),
        ])
    ;

    $services
        ->set(WorkflowClientInterface::class, WorkflowClient::class)
        ->args([
            service(ServiceClient::class),
        ])
    ;

    $services
        ->set(ServiceClient::class)
        ->factory([ServiceClient::class, 'create'])
        ->args([
            param('symfony_roadrunner_temporal.address'),
        ])
    ;
};
