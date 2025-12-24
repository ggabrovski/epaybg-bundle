<?php

namespace Otobul\EpaybgBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class OtobulEpaybgExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('manager.yaml');
        $loader->load('twig.yaml');
        $loader->load('controller.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('otobul_epaybg.epay_manager');
        $definition->setArgument(1, $config['min']);
        $definition->setArgument(2, $config['secret']);
        $definition->setArgument(3, $config['isDemo']);
    }
}
