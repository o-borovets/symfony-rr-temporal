<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\Runtime;

use RoadRunnerTemporalSymfony\TemporalWorkerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;

class TemporalRuntimeRunner implements RunnerInterface
{
    public function __construct(
        private readonly KernelInterface $kernel
    ) {
    }

    public function run(): int
    {
        $this->kernel->boot();

        /** @var TemporalWorkerInterface $workerRunner */
        $workerRunner = $this->kernel->getContainer()->get(TemporalWorkerInterface::class);

        return $workerRunner->run();
    }
}
