<?php

namespace MyBuilder\Bundle\CronosBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            // Symfony 4
            $treeBuilder = new TreeBuilder('my_builder_cronos');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Symfony 3
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('my_builder_cronos');
        }

        if (method_exists(Kernel::class, 'getProjectDir')) {
            // `kernel.project_dir` available since Symfony 3.3
            $pathToConsole = '%kernel.project_dir%/bin/console';
        } else {
            // `kernel.root_dir` dropped in Symfony 5
            $pathToConsole = '%kernel.root_dir%/../bin/console';
        }

        $rootNode
            ->children()
                ->arrayNode('exporter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('key')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Unique key that wraps all the cron configured for the current application.')
                            ->example('my_symfony_app')
                        ->end()
                        ->scalarNode('mailto')
                            ->cannotBeEmpty()
                            ->info('Sets the default email address for all cron output to go to.')
                            ->example('cron@example.com')
                        ->end()
                        ->scalarNode('path')
                            ->example('/usr/local/bin:/usr/bin:/bin')
                            ->info('Sets the path for all commands in the crontab.')
                        ->end()
                        ->scalarNode('executor')
                            ->cannotBeEmpty()
                            ->defaultValue('php')
                            ->example('php')
                            ->info('Allows you to specify a program that all commands should be passed to such as "/usr/local/bin/php".')
                        ->end()
                        ->scalarNode('console')
                            ->cannotBeEmpty()
                            ->defaultValue($pathToConsole)
                            ->example('%kernel.project_dir%/bin/console')
                            ->info('Allows you to specify the console that all commands should be passed to such as "bin/console".')
                        ->end()
                        ->scalarNode('shell')
                            ->cannotBeEmpty()
                            ->example('/bin/sh')
                            ->info('Allows you to specify which shell each program should be run with.')
                        ->end()
                        ->scalarNode('timezone')
                            ->example('Europe/Paris')
                            ->info('Allows you to add CRON_TZ which specifies the time zone specific for the cron table.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
