<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Minimal admin interface.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class AdminDefaultController extends Controller
{
    /**
     * Redirects to the actual competition's start page given by configuration.
     *
     * @param mixed $roundId id of the round
     *
     * @return array template parameters
     *
     * @todo move ROOT page and end slug to configuration
     *
     * @Route("/round/{round_id}", name = "admin_round" )
     * @Template
     */
    public function roundAction($roundId)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $roundRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Round');
        $submissionRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Submission');
        $roundRec = $roundRep->findOneById($roundId);

        $request = $this->container->get('request');
        if ($this->container->get('request')->getMethod() === 'POST') {
            $roundRec->setAdvanceNo($request->request->get('att'));
        }

        $standing = array();
        $users = array();

        $submissions = array();
        foreach ($submissionRep->getByRound($roundRec) as $subm) {
            if (!isset($submissions[$subm->getUserId()->getUniqueIdentifier()])) {
                $submissions[$subm->getUserId()->getUniqueIdentifier()] = array();
                $standing[$subm->getUserId()->getUniqueIdentifier()] = array();
            }
            $sR =& $submissions[$subm->getUserId()->getUniqueIdentifier()];

            $cat = $subm->getCategory();
            if ($cat=='undefined' || $cat == '') {
                $cat = 'default';
            }

            if (isset($sR[$cat])) {
                continue;
            }

            if ($this->container->get('request')->getMethod() === 'POST') {
                $subm->setPoints($request->request->get('r'.$subm->getId()));
            }

            $sR[$cat] = array($subm->getId(),$subm->getPoints());

            $p = (int) $subm->getPoints();
            $standing[$subm->getUserId()->getUniqueIdentifier()][]= $p;
            $users[$subm->getUserId()->getUniqueIdentifier()] = $subm->getUserId();
        }

        $em->flush();

        $standing2 = array();
        foreach ($standing as $k => $v) {
            $standing2[]= array($k, array_sum($v), $users[$k]);
        }

        usort($standing2, function($a,$b) {
            return ($b[1]-$a[1]);
        });

        $fwd = $roundRec->getAdvanceNo();


        return array(
            'submissions' => $submissions,
            'section' => $roundRec->getPage()->getParent(),
            'standing' => $standing2,
            'fwd' => $fwd
        );
    }


    /**
     * Redirects to the actual competition's start page given by configuration.
     *
     * @return array template parameters
     *
     * @todo move ROOT page and end slug to configuration
     *
     * @Route("/", name = "admin_index" )
     * @Template
     */
    public function indexAction()
    {
        $roundRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Round');

        return array(
            'rounds' => $roundRep->getSubmittedRounds()
        );
    }
}
