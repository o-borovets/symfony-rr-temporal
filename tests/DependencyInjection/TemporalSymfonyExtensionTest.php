<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RoadRunnerTemporalSymfony\DependencyInjection\TemporalSymfonyExtension;
use RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub\TestActivity;
use RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub\TestFinalizer;
use RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub\TestWorkflow;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 *
 * @covers \RoadRunnerTemporalSymfony\DependencyInjection\TemporalSymfonyExtension
 */
final class TemporalSymfonyExtensionTest extends TestCase
{
    protected ContainerBuilder|null $containerBuilder = null;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $extension = new TemporalSymfonyExtension();

        $this->containerBuilder->getCompilerPassConfig()->setOptimizationPasses([]);
        $this->containerBuilder->getCompilerPassConfig()->setRemovingPasses([]);
        $this->containerBuilder->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $this->containerBuilder->registerExtension($extension);
    }

    protected function tearDown(): void
    {
        $this->containerBuilder = null;
    }

    public function testActivityRegisteredAutoconfiguration()
    {
        $this->registerService(TestActivity::class);

        $this->loadAndCompile();

        $d = $this->containerBuilder->getDefinition(TestActivity::class);

        $tg = $d->getTag('temporal_symfony.activity');
        static::assertNotEmpty($tg);
    }

    public function testWorkflowRegisteredAutoconfiguration()
    {
        $this->registerService(TestWorkflow::class);

        $this->loadAndCompile();

        $definition = $this->containerBuilder->getDefinition(TestWorkflow::class);

        static::assertTrue($definition->hasTag('temporal_symfony.workflow'));
    }

    public function testFinalizerRegisteredAutoconfiguration()
    {
        $this->registerService(TestFinalizer::class);

        $this->loadAndCompile();

        $def = $this->containerBuilder->getDefinition(TestFinalizer::class);

        static::assertTrue($def->hasTag('temporal_symfony.finalizer'));
    }

    private function registerService(string $className): void
    {
        $this->containerBuilder->register($className)
            ->setPublic(true)
            ->setAutoconfigured(true)
        ;
    }

    private function loadAndCompile(array $additionalConfig = [])
    {
        foreach ($this->containerBuilder->getExtensions() as $extension) {
            $extension->load($additionalConfig, $this->containerBuilder);
        }

        $this->containerBuilder->compile();
    }
}
