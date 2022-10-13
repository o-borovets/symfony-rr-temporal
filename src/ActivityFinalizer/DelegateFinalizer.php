<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\ActivityFinalizer;

class DelegateFinalizer implements FinalizerInterface
{
    /** @var list<FinalizerInterface> */
    private array $registry;

    public function __construct(iterable $finalizers)
    {
        $this->registry = [];

        foreach ($finalizers as $finalizer) {
            if ($finalizer instanceof FinalizerInterface) {
                $this->addFinalizer($finalizer);
            }
        }
    }

    public function addFinalizer(FinalizerInterface $finalizer): void
    {
        $this->registry[] = $finalizer;
    }

    public function finalize(): void
    {
        foreach ($this->registry as $finalizer) {
            $finalizer->finalize();
        }
    }
}
