<?php
namespace Eotvos\VersenyrBundle\DependencyInjection\Mod;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RoundModCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('eotvos.versenyr.roundregistry')) {
            return;
        }
        $registry = $container->getDefinition('eotvos.versenyr.roundregistry');

        foreach ($container->findTaggedServiceIds('eotvos.versenyr.roundtype') as $id => $attributes) {
            $registry->addMethodCall('register', array($id, new Reference($id)));
        }
    }
}

