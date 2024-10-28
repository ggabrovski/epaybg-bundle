<?php

namespace Otobul\EpaybgBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('otobul_epaybg');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('min')->defaultValue('YOUR_MIN')->info('Identification number of the merchant')->end()
                ->scalarNode('secret')->defaultValue('YOUR_SECRET')->info('The secret word of the merchant')->end()
                ->booleanNode('isDemo')->defaultValue(true)->info('If true all requests will be sent to ePay.bg’s Demo System')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
