<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony;

use RoadRunnerTemporalSymfony\ActivityFinalizer\FinalizerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Temporal\Worker\WorkerFactoryInterface;

class TemporalWorkerRunner implements TemporalWorkerRunnerInterface
{
    public function __construct(
        private readonly WorkerFactoryInterface $workerFactory,
        private readonly ServiceLocator $activities,
        private readonly ServiceLocator $workflows,
        private readonly FinalizerInterface $finalizer,
    ) {
    }

    public function run(): int
    {
        $worker = $this->workerFactory->newWorker();

        $worker->registerWorkflowTypes(...array_values($this->workflows->getProvidedServices()));

        foreach ($this->activities->getProvidedServices() as $name => $type) {
            $worker->registerActivity($type, fn () => $this->activities->get($name));
        }

        $worker->registerActivityFinalizer(function () {
            $this->finalizer->finalize();
        });

        return $this->workerFactory->run();
    }
}
