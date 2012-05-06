<?php

namespace Eotvos\VersenyrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Default controller.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class DefaultController extends Controller
{
    /**
     * Redirects to the actual competition's start page given by configuration.
     *
     * @return array template parameters
     *
     * @author Zsolt Parragi <zsolt.parragi@cancellar.hu>
     * @since   2011-09-26
     * @version 2011-09-26
     *
     * @Route("/", name = "home" )
     */
    public function indexAction()
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Term');
        $currentTerm = $tpRep->getLastTerm();

        return $this->redirect($this->generateUrl('competition_page', array('term' => $currentTerm->getName(), 'pageSlug' => $currentTerm->getRootPage()->getSlug())));
    }
}
