<?php

namespace Eotvos\VersenyrBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;


use Eotvos\VersenyrBundle\DependencyInjection\Mod\RoundModCompilerPass;
use Eotvos\VersenyrBundle\DependencyInjection\Mod\RegistrationModCompilerPass;

/**
 * EotvosVersenyrBundle
 *
 * @uses      Bundle
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class EotvosVersenyrBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RoundModCompilerPass());
        $container->addCompilerPass(new RegistrationModCompilerPass());
    }

}
