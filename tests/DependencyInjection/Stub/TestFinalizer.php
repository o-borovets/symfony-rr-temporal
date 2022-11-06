<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub;

use RoadRunnerTemporalSymfony\ActivityFinalizer\FinalizerInterface;

class TestFinalizer implements FinalizerInterface
{
    public function finalize(): void
    {
    }
}
