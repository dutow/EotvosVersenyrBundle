<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Eotvos\VersenyrBundle\Entity\TextPage;
use Eotvos\VersenyrBundle\Entity\Section;
use Eotvos\VersenyrBundle\Entity\Round;
use Eotvos\VersenyrBundle\Form\Type\TextPageType;
use Eotvos\VersenyrBundle\Form\Type\SectionType;
use Eotvos\VersenyrBundle\Form\Type\RoundType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Minimal admin interface for textpages.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @Route("/admin/textpage");
 */
class TextPageController extends Controller
{
    /**
     * Shows textpage list
     *
     * @return array template parameters
     *
     * @Route("/", name = "admin_textpage_index" )
     * @Template
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;

        $request = $this->get('request');
        $type = $request->query->get('type', 'all');

        if (!in_array($type, array('all'))) {
            throw $this->createNotFoundException("Bad parameter");
        }

        switch ($type) {

            case 'all':
                $textpages = $repo->getTreeList();
                break;
        }

        return array(
            'textpages' => $textpages,
            'container' => $this->container,
        );
    }

    /**
     * Delete a textpage
     *
     * @param int $id Id of the textpage
     *
     * @return array of template parameters
     *
     * @Route("/delete/{id}", name = "admin_textpage_delete" )
     * @Template
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("TextPage not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;
        $textpage =  $repo->findOneById($id);

        if (!$textpage) {
            throw $this->createNotFoundException("TextPage not found");
        }

        if (!$textpage->isDeletable()) {
            throw $this->createNotFoundException("This special textpage can't be deleted!");
        }

        $em->remove($textpage);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_textpage_index'));
    }

    /**
     * Creates a new textpage
     *
     * @return array of template parameters
     *
     * @Route("/new", name = "admin_textpage_new" )
     * @Template
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;

        $request = $this->get('request');

        $subtype = $request->query->get('type', null);
        $parentId = $request->query->get('parent', null);
        if ($parentId!==null) {
            $parent = $repo->findOneById($parentId);
            if (!$parent) {
                throw $this->createNotFoundException("Parent not found");
            }
        }

        if (!in_array($subtype, array(null, 'round', 'section'))) {
            throw $this->createNotFoundException("Illegal type parameter");
        }

        $subform = null;
        $textpage = new TextPage();
        if ("section" == $subtype) {
            if ("sections"!=$parent->getSpecial()) {
                throw $this->createNotFoundException("Illegal parent page");
            }
            $subform = new SectionType($this->container);
            $textpage->setSection(new Section());
            $textpage->setSpecial($subtype);
            $textpage->setParent($parent);
            $textpage->setInMenu(false);
            $textpage->setFbbox(false);
        }
        if ("round" == $subtype) {
            if ("section"!=$parent->getSpecial()) {
                throw $this->createNotFoundException("Illegal parent page");
            }
            $subform = new RoundType($this->container);
            $textpage->setRound(new Round());
            $textpage->setSpecial($subtype);
            $textpage->setParent($parent);
            $textpage->setInMenu(false);
            $textpage->setFbbox(false);
        }
        $form = $this->createForm(new TextPageType($this->container, $subtype, $subform), $textpage);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $textpage = $form->getData();

                $em->persist($textpage);

                if ("section" == $subtype) {
                    $em->persist($textpage->getSection());
                }
                if ("round" == $subtype) {
                    $em->persist($textpage->getRound());
                }

                $em->flush();
                if ("section" == $subtype) {
                    $section = $textpage->getSection();
                    $section->setPage($textpage);
                    $em->persist($section);
                }
                if ("round" == $subtype) {
                    $round = $textpage->getRound();
                    $round->setPage($textpage);
                    $em->persist($round);
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_textpage_index'));
            }else{
            }
        }

        return array(
            'form' => $form->createView(),
            'textpage' => $textpage,
        );
    }

    /**
     * Editing a textpage
     *
     * @param int $id Id of the textpage
     *
     * @return array of template parameters
     *
     * @Route("/edit/{id}", name = "admin_textpage_edit" )
     * @Template
     */
    public function editAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("TextPage not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;

        $textpage = $repo->findOneById($id);

        if (!$textpage) {
            throw $this->createNotFoundException("TextPage not found");
        }

        $subform = null;
        if ("section" == $textpage->getSpecial()) {
            $subform = new SectionType($this->container);
        }
        if ("round" == $textpage->getSpecial()) {
            $subform = new RoundType($this->container);
        }
        $form = $this->createForm(new TextPageType($this->container, $textpage->getSpecial(), $subform), $textpage);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $textpage = $form->getData();

                if ($textpage->getId()!=$id) {
                    throw $this->createNotFoundException("Don't forge stupid updates");
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_textpage_index'));
            }
        }

        return array(
            'textpage' => $textpage,
            'form' => $form->createView(),
        );
    }


    /**
     * Moves a record upwards
     *
     * @param int $id Id of the textpage
     *
     * @return array of template parameters
     *
     * @Route("/moveup/{id}", name = "admin_textpage_moveup" )
     * @Template
     */
    public function moveUpAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("TextPage not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;

        $textpage = $repo->findOneById($id);

        if (!$textpage) {
            throw $this->createNotFoundException("TextPage not found");
        }

        if ($textpage->isFirstChild()) {
            throw $this->createNotFoundException("First child can't be moved");
        }

        $repo->moveUp($textpage);

        $em->flush();

        return $this->redirect($this->generateUrl('admin_textpage_index'));
    }

    /**
     * Moves a record downwards
     *
     * @param int $id Id of the textpage
     *
     * @return array of template parameters
     *
     * @Route("/movedown/{id}", name = "admin_textpage_movedown" )
     * @Template
     */
    public function moveDownAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("TextPage not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;

        $textpage = $repo->findOneById($id);

        if (!$textpage) {
            throw $this->createNotFoundException("TextPage not found");
        }

        if ($textpage->isLastChild()) {
            throw $this->createNotFoundException("Last child can't be moved");
        }

        $repo->moveDown($textpage);

        $em->flush();

        return $this->redirect($this->generateUrl('admin_textpage_index'));
    }

    /**
     * Shows a textpage
     *
     * @param int $id Id of the textpage
     *
     * @return array of template parameters
     *
     * @Route("/{id}", name = "admin_textpage_show" )
     * @Template
     */
    public function showAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("TextPage not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:TextPage')
            ;

        $textpage = $repo->findOneById($id);

        if (!$textpage) {
            throw $this->createNotFoundException("TextPage not found");
        }

        return array(
            'textpage' => $textpage,
        );
    }
}
