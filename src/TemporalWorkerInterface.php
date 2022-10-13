<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony;

interface TemporalWorkerInterface
{
    public function run(): int;
}
