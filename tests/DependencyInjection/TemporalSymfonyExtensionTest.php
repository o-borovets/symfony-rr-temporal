<?php

declare(strict_types=1);

namespace RoadRunnerTemporalSymfony\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use RoadRunnerTemporalSymfony\ActivityFinalizer\FinalizerInterface;
use RoadRunnerTemporalSymfony\DependencyInjection\TemporalSymfonyExtension;
use RoadRunnerTemporalSymfony\TemporalWorkerRunnerInterface;
use RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub\TestActivity;
use RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub\TestFinalizer;
use RoadRunnerTemporalSymfony\Tests\DependencyInjection\Stub\TestWorkflow;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Temporal\Worker\WorkerFactoryInterface;

/**
 * @internal
 *
 * @covers \RoadRunnerTemporalSymfony\DependencyInjection\TemporalSymfonyExtension
 *
 * @noinspection PhpUnnecessaryStaticReferenceInspection
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

    public function testRunnerConfiguration()
    {
        $this->registerService(TestActivity::class);
        $this->registerService(TestWorkflow::class);

        $this->loadAndCompile();

        $d = $this->containerBuilder->getDefinition(TemporalWorkerRunnerInterface::class);

        $factoryDef = $d->getArgument(0);
        static::assertInstanceOf(Reference::class, $factoryDef);
        static::assertSame(WorkerFactoryInterface::class, (string)$factoryDef);

        $activityLocatorDef = $d->getArgument(1);
        static::assertInstanceOf(ServiceLocatorArgument::class, $activityLocatorDef);
        static::assertSame('temporal_symfony.activity', $activityLocatorDef->getTaggedIteratorArgument()->getTag());

        $activityLocatorDef = $d->getArgument(2);
        static::assertInstanceOf(ServiceLocatorArgument::class, $activityLocatorDef);
        static::assertSame('temporal_symfony.workflow', $activityLocatorDef->getTaggedIteratorArgument()->getTag());

        $factoryDef = $d->getArgument(3);
        static::assertInstanceOf(Reference::class, $factoryDef);
        static::assertSame(FinalizerInterface::class, (string)$factoryDef);
    }

    public function testActivityRegisteredAutoconfiguration()
    {
        $this->registerService(TestActivity::class);

        $this->loadAndCompile();

        $d = $this->containerBuilder->getDefinition(TestActivity::class);
        static::assertTrue($d->hasTag('temporal_symfony.activity'));
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
