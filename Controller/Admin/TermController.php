<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Eotvos\VersenyrBundle\Entity\Term;
use Eotvos\VersenyrBundle\Form\Type\TermType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Minimal admin interface for terms.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @Route("/admin/term");
 */
class TermController extends Controller
{
    /**
     * Shows term list
     *
     * @return array template parameters
     *
     * @Route("/", name = "admin_term_index" )
     * @Template
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $request = $this->get('request');
        $type = $request->query->get('type', 'all');

        if (!in_array($type, array('all'))) {
            throw $this->createNotFoundException("Bad parameter");
        }

        switch ($type) {

            case 'all':
                $terms = $repo->findAll();
                break;
        }

        return array(
            'terms' => $terms,
        );
    }

    /**
     * Delete a term
     *
     * @param int $id Id of the term
     *
     * @return array of template parameters
     *
     * @Route("/delete/{id}", name = "admin_term_delete" )
     * @Template
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Term not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;
        $term =  $repo->findOneById($id);

        if (!$term) {
            throw $this->createNotFoundException("Term not found");
        }

        $em->remove($term);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_term_index'));
    }

    /**
     * Creates a new term
     *
     * @return array of template parameters
     *
     * @Route("/new", name = "admin_term_new" )
     * @Template
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $term = new Term();
        $form = $this->createForm(new TermType($this->container), $term);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $term = $form->getData();
                $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                $passwordGenerator->encodePassword($term, $term->getPassword());

                $em->persist($term);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_term_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Editing a term
     *
     * @param int $id Id of the term
     *
     * @return array of template parameters
     *
     * @Route("/edit/{id}", name = "admin_term_edit" )
     * @Template
     */
    public function editAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Term not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $term = $repo->findOneById($id);

        if (!$term) {
            throw $this->createNotFoundException("Term not found");
        }

        $form = $this->createForm(new TermType($this->container), $term);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $term = $form->getData();

                if ($term->getId()!=$id) {
                    throw $this->createNotFoundException("Don't forge stupid updates");
                }

                if ($term->getPassword()) {
                    $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                    $passwordGenerator->encodePassword($term, $term->getPassword());
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_term_index'));
            }
        }

        return array(
            'term' => $term,
            'form' => $form->createView(),
        );
    }

    /**
     * Shows a term
     *
     * @param int $id Id of the term
     *
     * @return array of template parameters
     *
     * @Route("/{id}", name = "admin_term_show" )
     * @Template
     */
    public function showAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("Term not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $term = $repo->findOneById($id);

        if (!$term) {
            throw $this->createNotFoundException("Term not found");
        }

        return array(
            'term' => $term,
        );
    }

}
