<?php

namespace Eotvos\VersenyrBundle\Controller\Admin;

use Eotvos\VersenyrBundle\Entity\User;
use Eotvos\VersenyrBundle\Form\Type\AdminUserType;

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
 *
 * @Route("/admin/user");
 */
class UserController extends Controller
{
    /**
     * Shows user list
     *
     * @return array template parameters
     *
     * @Route("/", name = "admin_user_index" )
     * @Template
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $request = $this->get('request');
        $type = $request->query->get('type', 'all');

        if (!in_array($type, array('all', 'admin', 'tester', 'user'))) {
            throw $this->createNotFoundException("Bad parameter");
        }

        switch ($type) {
            case 'admin':
                $users = $repo->findByAdmin(true);
                break;

            case 'tester':
                $users = $repo->findByTester(true);
                break;

            case 'user':
                $users = $repo->getAllContensants();
                break;

            case 'all':
                $users = $repo->findAll();
                break;
        }

        return array(
            'users' => $users,
        );
    }

    /**
     * Delete a user
     *
     * @param int $id Id of the user
     *
     * @return array of template parameters
     *
     * @Route("/delete/{id}", name = "admin_user_delete" )
     * @Template
     */
    public function deleteAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("User not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;
        $user =  $repo->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        $em->remove($user);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_user_index'));
    }

    /**
     * Creates a new user
     *
     * @return array of template parameters
     *
     * @Route("/new", name = "admin_user_new" )
     * @Template
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $user = new User();
        $form = $this->createForm(new AdminUserType(), $user);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $user = $form->getData();
                $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                $passwordGenerator->encodePassword($user, $user->getPassword());

                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_user_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Editing a user
     *
     * @param int $id Id of the user
     *
     * @return array of template parameters
     *
     * @Route("/edit/{id}", name = "admin_user_edit" )
     * @Template
     */
    public function editAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("User not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $user = $repo->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        $form = $this->createForm(new AdminUserType(), $user);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $user = $form->getData();

                if ($user->getId()!=$id) {
                    throw $this->createNotFoundException("Don't forge stupid updates");
                }

                if ($user->getPassword()) {
                    $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                    $passwordGenerator->encodePassword($user, $user->getPassword());
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_user_index'));
            }
        }

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    /**
     * Shows a user
     *
     * @param int $id Id of the user
     *
     * @return array of template parameters
     *
     * @Route("/{id}", name = "admin_user_show" )
     * @Template
     */
    public function showAction($id)
    {
        if (!is_numeric($id)) {
            throw $this->createNotFoundException("User not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $user = $repo->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        return array(
            'user' => $user,
        );
    }

}