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
class EcvUrlExtension extends \Twig_Extension implements ContainerAwareInterface
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
     * Returns the list of the filters implemented by this class.
     * 
     * @return array list of filters
     */
    public function getFilters()
    {
        return array(
            'ecvurl' => new \Twig_Filter_Method($this, 'ecvurl')
        );
    }

    /**
     * Returns the functions implemented by this class.
     * 
     * @return array list of functions
     */
    public function getFunctions()
    {
        return array(
            'ecvroot' => new \Twig_Function_Method($this, 'getTermRoot'),
            'roundcontroller' => new \Twig_Function_Method($this, 'getRoundController'),
        );
    }

    /**
     * Returns the name of this extension
     * 
     * @return string name
     */
    public function getName()
    {
        return "EcvUrlExtension";
    }

    /**
     * Generates an URL to the given parameter based on it's class.
     *
     * It is used as a filter, e.g.: {{object|ecvurl}}
     * 
     * @param mixed $sentence base object
     * 
     * @return string
     */
    public function ecvurl($sentence)
    {
        if ($sentence instanceof Entity\Section) {
            $sentence = $sentence->getPage();
        }
        if ($sentence instanceof Entity\Round) {
            $sentence = $sentence->getPage();
        }
        if ($sentence instanceof Entity\TextPage) {

            if ($sentence->getSpecial()!="") {
                if ($sentence->getSpecial()=="jelentkezes") {
                    return $this->container->get('router')->generate('competition_'.$sentence->getSpecial(), array( 'term' => $sentence->getRootNode()->getTitle() ));
                } else {
                    return $this->container->get('router')->generate('competition_'.$sentence->getSpecial(), array( 'term' => $sentence->getRootNode()->getTitle(), 'slug' => $sentence->getSlug() ));
                }
            }
            if ($sentence->getSection()!=null) {
                // section handler, todo
                return $this->container->get('router')->generate('competition_section', array( 'term' => $sentence->getRootNode()->getTitle(), 'sectionSlug' => $sentence->getSlug()));
            }
            if ($sentence->getRound()!=null) {
                return $this->container->get('router')->generate('competition_round', array( 'term' => $sentence->getRootNode()->getTitle(), 'roundSlug' => $sentence->getSlug(), 'sectionSlug' => $sentence->getParent()->getSlug() ));
            }

            return $this->container->get('router')->generate('competition_page', array( 'term' => $sentence->getRootNode()->getTitle(), 'pageSlug' => $sentence->getSlug() ));
        } else {
            return "WTF";
        }
    }

    /**
     * Gets the root item for a given term.
     *
     * @param string $term term identifier, or null for latest
     * 
     * @return TextPage root element
     */
    public function getTermRoot($term=null)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        return $em->getRepository('EotvosVersenyrBundle:TextPage')->getTermRoot($term);
    }


    /**
     * Gets the controller for a round based on it's subclass.
     * 
     * @param mixed $round Subject round
     * 
     * @return Controller
     */
    public function getRoundController($round)
    {
        $roundController = $this->container->get('eotvos_versenyr.round.'.$round->getRoundtype());

        return $roundController;
    }

}
