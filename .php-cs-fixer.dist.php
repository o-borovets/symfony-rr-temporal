<?php

$finder = PhpCsFixer\Finder::create()
//    ->exclude('somedir')
//    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->notPath('src/DependencyInjection/Configuration.php')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

$config = new PhpCsFixer\Config();
//$config->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache');

return $config->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
