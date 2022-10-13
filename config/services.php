<?php

declare(strict_types=1);

use RoadRunnerTemporalSymfony\TemporalWorker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
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
        ->set(\RoadRunnerTemporalSymfony\TemporalWorkerInterface::class, \RoadRunnerTemporalSymfony\TemporalWorker::class)
        ->public()
        ->args([
            service('kernel'),
            tagged_locator('temporal_symfony.workflow'),
            tagged_locator('temporal_symfony.activity'),
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
