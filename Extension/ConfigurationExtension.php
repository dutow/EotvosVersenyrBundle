<?php

namespace Eotvos\VersenyrBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Eotvos\VersenyrBundle\Entity as Entity;

/**
 * Url helper functions for easy url generation in Twig views.
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class ConfigurationExtension extends \Twig_Extension implements ContainerAwareInterface
{

    protected $container; // service container

    /**
     * Sets the container, used by DI.
     * 
     * @param ContainerInterface $container Service Container
     * 
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the global variables defined by this extension
     * 
     * @return array list of functions
     */
    public function getGlobals()
    {
        $list = $this->container->get('doctrine')->getRepository('EotvosVersenyrBundle:Configuration')->findAll();

        $ret = array();

        foreach ($list as $item) {
            $ret[str_replace('.', '_', $item->getName())] = $item->getValue();
        }


        return $ret;
    }

    /**
     * Returns the name of this extension
     * 
     * @return string name
     */
    public function getName()
    {
        return "ConfigurationExtension";
    }

}
