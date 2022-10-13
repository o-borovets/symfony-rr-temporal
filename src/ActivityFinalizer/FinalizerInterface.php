<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\ActivityFinalizer;

interface FinalizerInterface
{
    public function finalize(): void;
}
