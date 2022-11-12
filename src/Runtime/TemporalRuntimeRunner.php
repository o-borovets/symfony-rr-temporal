<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\Runtime;

use RoadRunnerTemporalSymfony\TemporalWorkerRunnerInterface;
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

        /** @var TemporalWorkerRunnerInterface $workerRunner */
        $workerRunner = $this->kernel->getContainer()->get(TemporalWorkerRunnerInterface::class);

        return $workerRunner->run();
    }
}
