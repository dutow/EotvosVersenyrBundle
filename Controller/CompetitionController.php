<?php

namespace Eotvos\VersenyrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Controller for general competition related tasks.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu>
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class CompetitionController extends Controller
{

    /**
     * Renders the section list for the given term.
     *
     * @param int $term Competition term
     *
     * @return array template parameters
     *
     * @todo Move 'szekciok' slug to config.
     *
     * @Route("/{term}/szekciok", name="competition_sections" )
     * @Template()
     */
    public function sectionsAction($term)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $pageRec = $tpRep->getForTermWithSpecial($term, 'sections');


        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }


        if (!$pageRec) {
            throw $this->createNotFoundException('page not found');
        }

        $secRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Section');
        $sectionsRec = $secRep->getForTerm($term->getName());

        if (count($sectionsRec) == 0) {
            throw $this->createNotFoundException('page not found');
        }

        return array(
            'page' => $pageRec,
            'sections' => $sectionsRec,
            'term' => $term,
        );
    }

    /**
     * Renders a section identified by the term and the sectionSlug.
     *
     * @param int    $term        Competition term
     * @param string $sectionSlug section's url name
     *
     * @return array template parameters
     *
     * @Route("/{term}/szekcio/{sectionSlug}", name="competition_section" )
     * @Template()
     */
    public function sectionAction($term, $sectionSlug)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $sectionRec = $tpRep->getForTermWithSlug($term, $sectionSlug);


        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        if (!$sectionRec || $sectionRec->getSection()==null) {
            throw $this->createNotFoundException('page not found');
        }

        return array(
            'page' => $sectionRec,
            'section' => $sectionRec->getSection(),
            'term' => $term,
        );
    }

    /**
     * Summa of round.
     *
     * @param mixed $term        term of the round
     * @param mixed $sectionSlug section of round
     * @param mixed $roundSlug   round name
     * 
     * @return array template parameters
     *
     * @Route("/{term}/szekcio/{sectionSlug}/fordulo/{roundSlug}/sum", name="competition_round_sum" )
     * @Template()
     */
    public function sumAction($term, $sectionSlug, $roundSlug)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $roundRec = $tpRep->getForTermWithSlug($term, $roundSlug);
        $sectionRec = $tpRep->getForTermWithSlug($term, $sectionSlug);
        $submissionRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Submission');

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        if (!$sectionRec || $sectionRec->getSection()==null || !$roundRec || $roundRec->getRound()==null || $roundRec->getParent()->getId()!=$sectionRec->getId()) {
            throw $this->createNotFoundException('page not found');
        }

        $submissions = array();
        $submissions = $submissionRep->getStandingByRound($roundRec->getRound());
        $roundtype = $this->get($roundRec->getRound()->getRoundtype());
        $standing = $roundtype->orderStanding($submissions, $roundRec->getRound());



        return array(
            'round' => $roundRec->getRound(),
            'section' => $sectionRec->getSection(),
            'standing' => $standing,
            'page' => $roundRec,
            'term' => $term,
        );
    }


    /**
     * Renders a round of a section identified by the term and the roundSlug. The sectionSlug is just here for santify check and prettier URLs.
     *
     * @param int    $term        Competition term
     * @param string $sectionSlug Section name
     * @param string $roundSlug   Round name
     *
     * @return array template parameters
     *
     * @Route("/{term}/szekcio/{sectionSlug}/fordulo/{roundSlug}", name="competition_round" )
     * @Template()
     */
    public function roundAction($term, $sectionSlug, $roundSlug)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $roundRec = $tpRep->getForTermWithSlug($term, $roundSlug);
        $sectionRec = $tpRep->getForTermWithSlug($term, $sectionSlug);

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        if (!$sectionRec || $sectionRec->getSection()==null || !$roundRec || $roundRec->getRound()==null || $roundRec->getParent()->getId()!=$sectionRec->getId()) {
            throw $this->createNotFoundException('page not found');
        }

        return array(
            'round' => $roundRec->getRound(),
            'section' => $sectionRec->getSection(),
            'page' => $roundRec,
            'term' => $term,
        );
    }

    /**
     * Renders a simple text page from the database. If no page exists for the given term and slug, renders a 404 error page.
     *
     * @param int    $term     Competition term
     * @param string $pageSlug page's name
     *
     * @return array template parameters
     *
     * @Route("/{term}/{pageSlug}", name="competition_page", requirements = {"slug" = "[\w-]+" } )
     * @Template()
     */
    public function textpageAction($term, $pageSlug)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $pageRec = $tpRep->getForTermWithSlug($term, $pageSlug);

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        if ($pageRec===null) {
            throw $this->createNotFoundException("Page $pageSlug not found!");
        }

        if ($pageRec->getRedirectsTo()!="") {
            return $this->redirect($this->generateUrl('competition_page', array('term' => $term->getId(), 'pageSlug' => $pageRec->getRedirectsTo())));
        }

        if (!$pageRec) {
            throw $this->createNotFoundException('page not found');
        }

        return array(
            'page' => $pageRec,
            'term' => $term,
        );
    }

    /**
     * archives
     * 
     * @return array of template parameters
     *
     * @Route("/archives", name="archives" )
     * @Template()
     */
    public function archivesAction()
    {
        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->getLastTerm()
            ;

        $terms = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findAll()
            ;

        return array(
            'terms' => $terms,
            'term' => $term,
        );
    }
    
}
