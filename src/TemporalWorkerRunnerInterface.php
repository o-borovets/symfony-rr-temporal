<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony;

interface TemporalWorkerRunnerInterface
{
    public function run(): int;
}
