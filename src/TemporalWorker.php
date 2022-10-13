<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Temporal\Worker\WorkerFactoryInterface;

class TemporalWorker implements TemporalWorkerInterface
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly ServiceLocator $activities,
        private readonly ServiceLocator $workflows,
    ) {
    }

    public function run(): int
    {
        /** @var WorkerFactoryInterface $workerFactory */
        $workerFactory = $this->kernel->getContainer()->get(WorkerFactoryInterface::class);

        $worker = $workerFactory->newWorker();

        $worker->registerWorkflowTypes(...array_values($this->workflows->getProvidedServices()));

        foreach ($this->activities->getProvidedServices() as $name => $type) {
            $worker->registerActivity($type, fn () => $this->activities->get($name));
        }

        $worker->registerActivityFinalizer(function () {
            /** @var ContainerInterface $container */
            $container = $this->kernel->getContainer();

            if ($container->has('services_resetter')) {
                $container->get('services_resetter')->reset();
            }
        });

        return $workerFactory->run();
    }
}
