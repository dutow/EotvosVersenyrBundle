<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Eotvos\VersenyrBundle\Entity\TextPage;
use Eotvos\VersenyrBundle\Form\Type\TextPageType;

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

        $textpage = new TextPage();
        $form = $this->createForm(new TextPageType(), $textpage);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $textpage = $form->getData();
                $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                $passwordGenerator->encodePassword($textpage, $textpage->getPassword());

                $em->persist($textpage);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_textpage_index'));
            }
        }

        return array(
            'form' => $form->createView(),
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

        $form = $this->createForm(new TextPageType(), $textpage);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $textpage = $form->getData();

                if ($textpage->getId()!=$id) {
                    throw $this->createNotFoundException("Don't forge stupid updates");
                }

                if ($textpage->getPassword()) {
                    $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                    $passwordGenerator->encodePassword($textpage, $textpage->getPassword());
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
