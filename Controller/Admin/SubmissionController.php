<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Eotvos\VersenyrBundle\Entity\Submission;
use Eotvos\VersenyrBundle\Form\Type\SubmissionType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Minimal admin interface for submissions.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @Route("/admin/submission");
 */
class SubmissionController extends Controller
{
    /**
     * Shows submission list
     *
     * @param id $round round id
     *
     * @return array template parameters
     *
     * @Route("/", name = "admin_submission_index", defaults = { "round"= null } )
     * @Route("/{round}", name = "admin_submission_indexr" )
     * @Template
     */
    public function indexAction($round)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Submission')
            ;

        $rounds = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Round')
            ->findAll()
            ;

        if (null!=$round) {
            $round = $this->getDoctrine()->getRepository('EotvosVersenyrBundle:Round')->findOneById($round);
            if (!$round) {
                throw $this->createNotFoundException("Bad parameter");
            }
        }

        if ($round) {
            $submissions = $repo->getLastUniqByRound($round);
        } else {
            $submissions = array();
        }

        return array(
            'submissions' => $submissions,
            'rounds' => $rounds,
            'thisround' => $round,
        );
    }

    /**
     * Generates advancers list
     *
     * @param id $round round id
     *
     * @return array template parameters
     *
     * @Route("/generate/{round}", name = "admin_submission_generate" )
     * @Template
     */
    public function generateAction($round)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Submission')
            ;

        $request = $this->get('request');

        $round = $this->getDoctrine()->getRepository('EotvosVersenyrBundle:Round')->findOneById($round);
        if (!$round) {
            throw $this->createNotFoundException("Bad parameter");
        }

        $allSub = $repo->getLastByRound($round);

        // by default nobody advances
        foreach ($allSub as $sub) {
            $sub->setAdvances(1);
        }

        $roundtype = $this->container->get($round->getRoundtype());

        $standing = $roundtype->orderStanding($repo->getStandingByRound($round));

        // first N advances
        for ($i = 0; $i < $round->getAdvanceNo(); $i++) {
            if ($i >= count($standing)) {
                break;
            }
            foreach ($standing[$i][1] as $sub) {
                $sub->setAdvances(2);
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('admin_submission_indexr', array( 'round' => $round->getId())));
    }

    /**
     * Delete a submission
     *
     * @param int $id Id of the submission
     *
     * @return array of template parameters
     *
     * @Route("/delete/{id}", name = "admin_submission_delete" )
     * @Template
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Submission not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Submission')
            ;
        $submission =  $repo->findOneById($id);

        if (!$submission) {
            throw $this->createNotFoundException("Submission not found");
        }

        $em->remove($submission);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_submission_indexr', array( 'round' => $submission->getRound()->getId())));
    }

    /**
     * Creates a new submission
     *
     * @param int $round Id of the parent round
     *
     * @return array of template parameters
     *
     * @Route("/new/{round}", name = "admin_submission_new" )
     * @Template
     */
    public function newAction($round)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Submission')
            ;

        $request = $this->get('request');

        $round = $this->getDoctrine()->getRepository('EotvosVersenyrBundle:Round')->findOneById($round);
        if (!$round) {
            throw $this->createNotFoundException("Bad parameter");
        }

        $submission = new Submission();
        $submission->setRound($round);
        $submission->setData('{}'); // empty data for user submitted records
        $form = $this->createForm(new SubmissionType($this->container, $round), $submission);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $submission = $form->getData();

                $em->persist($submission);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_submission_indexr', array( 'round' => $round->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'round' => $round,
        );
    }

    /**
     * Editing a submission
     *
     * @param int $id Id of the submission
     *
     * @return array of template parameters
     *
     * @Route("/edit/{id}", name = "admin_submission_edit" )
     * @Template
     */
    public function editAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Submission not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Submission')
            ;

        $submission = $repo->findOneById($id);

        if (!$submission) {
            throw $this->createNotFoundException("Submission not found");
        }

        $round = $submission->getRound();

        $form = $this->createForm(new SubmissionType($this->container, $round), $submission);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $submission = $form->getData();

                if ($submission->getId()!=$id) {
                    throw $this->createNotFoundException("Don't forge stupid updates");
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_submission_indexr', array( 'round' => $round->getId())));
            }
        }

        return array(
            'submission' => $submission,
            'form' => $form->createView(),
        );
    }

    /**
     * Shows a submission
     *
     * @param int $id Id of the submission
     *
     * @return array of template parameters
     *
     * @Route("/show/{id}", name = "admin_submission_show" )
     * @Template
     */
    public function showAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Submission not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Submission')
            ;

        $submission = $repo->findOneById($id);

        if (!$submission) {
            throw $this->createNotFoundException("Submission not found");
        }

        return array(
            'submission' => $submission,
        );
    }

}
