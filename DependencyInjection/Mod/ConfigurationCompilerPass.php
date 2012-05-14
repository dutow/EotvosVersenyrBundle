<?php

namespace Eotvos\VersenyrBundle\DependencyInjection\Mod;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RegistrationModCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('eotvos.versenyr.registrationregistry')) {
            return;
        }
        $registry = $container->getDefinition('eotvos.versenyr.registrationregistry');

        foreach ($container->findTaggedServiceIds('eotvos.versenyr.registrationtype') as $id => $attributes) {
            $registry->addMethodCall('register', array($id, new Reference($id)));
        }
    }
}

