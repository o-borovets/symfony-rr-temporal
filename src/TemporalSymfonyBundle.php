<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony;

use RoadRunnerTemporalSymfony\DependencyInjection\CompilerPass\ActivityFinalizerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TemporalSymfonyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new ActivityFinalizerPass())
        ;

        parent::build($container);
    }
}
