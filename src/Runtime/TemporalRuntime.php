<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\Runtime;

use Spiral\RoadRunner\Environment\Mode;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Runtime\RunnerInterface;
use Symfony\Component\Runtime\SymfonyRuntime;

class TemporalRuntime extends SymfonyRuntime
{
    public function getRunner(null|object $application): RunnerInterface
    {
        if ($application instanceof KernelInterface && Mode::MODE_TEMPORAL === getenv('RR_MODE')) {
            return new TemporalRuntimeRunner($application);
        }

        return parent::getRunner($application);
    }
}
