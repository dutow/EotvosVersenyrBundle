<?php

namespace Eotvos\VersenyrBundle\DependencyInjection\Mod;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ResultModCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('eotvos.versenyr.resultregistry')) {
            return;
        }
        $registry = $container->getDefinition('eotvos.versenyr.resultregistry');

        foreach ($container->findTaggedServiceIds('eotvos.versenyr.resulttype') as $id => $attributes) {
            $registry->addMethodCall('register', array($id, new Reference($id)));
        }
    }
}

