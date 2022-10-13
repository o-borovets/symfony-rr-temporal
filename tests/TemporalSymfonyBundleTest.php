<?php

namespace RoadRunnerTemporalSymfony\Tests;

use RoadRunnerTemporalSymfony\TemporalSymfonyBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class TemporalSymfonyBundleTest extends TestCase
{
    public function test_bundle_expose_the_activity_worker()
    {
        $k = $this->getKernel();
        $k->boot();
        $c = $k->getContainer()->get('test.service_container');

        $cmd = $c->get(\RoadRunnerTemporalSymfony\TemporalWorkerInterface::class);

        $this->assertInstanceOf(\RoadRunnerTemporalSymfony\TemporalWorkerInterface::class, $cmd);
    }


    /**
     * @param BundleInterface[] $extraBundles
     */
    public function getKernel(array $config = [], array $extraBundles = []): KernelInterface
    {
        return new class('test', true, $config, $extraBundles) extends Kernel {
            use MicroKernelTrait;

            private $config;
            private $extraBundles;

            public function __construct(string $env, bool $debug, array $config, array $extraBundles)
            {
                (new Filesystem())->remove(__DIR__.'/__cache');

                parent::__construct($env, $debug);

                $this->config = $config;
                $this->extraBundles = $extraBundles;
            }

            public function getCacheDir(): string
            {
                return __DIR__.'/__cache';
            }

            public function registerBundles(): iterable
            {
                yield new FrameworkBundle();

                yield from $this->extraBundles;

                yield new TemporalSymfonyBundle();
            }

            protected function configureRoutes(RouteCollectionBuilder $routes)
            {
            }

            protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
            {
                $c->setParameter('container.dumper.inline_factories', true);

                $c->loadFromExtension('framework', [
                    'test' => true,
                    'secret' => 'secret',
                ]);

                foreach ($this->config as $key => $config) {
                    $c->loadFromExtension($key, $config);
                }
            }
        };
    }
}
