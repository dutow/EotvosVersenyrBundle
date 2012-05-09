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
     * Deletes a registration
     * 
     * @param int $user user id
     * @param int $term term id
     * 
     * @return array template parameters
     *
     * @Route("/delreg/{user}/{term}", name = "admin_user_delreg" )
     * @Template
     */
    public function deleteRegistrationAction($user, $term)
    {
        if (!is_numeric($user)) {
            throw $this->createNotFoundException("User not found");
        }

        if (!is_numeric($term)) {
            throw $this->createNotFoundException("Term not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $userRepo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $user = $userRepo->findOneById($user);

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        $termRepo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $term = $termRepo->findOneById($term);

        if (!$term) {
            throw $this->createNotFoundException("Term not found");
        }

        foreach ($user->getRegistrations() as $registration) {
            if ($registration->getTerm()==$term) {
                // found it -> delete
                $em->remove($registration);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $user->getId())));
            }
        }

        throw $this->createNotFoundException("Regisration not found!");
    }

    /**
     * Registers a user to a term
     * 
     * @param int $user user id
     * @param int $term term id
     * 
     * @return array template parameters
     *
     * @Route("/register/{user}/{term}", name = "admin_user_register" )
     * @Template
     */
    public function registerAction($user, $term)
    {
        if (!is_numeric($user)) {
            throw $this->createNotFoundException("User not found");
        }

        if (!is_numeric($term)) {
            throw $this->createNotFoundException("Term not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $userRepo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $user = $userRepo->findOneById($user);

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        $termRepo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $term = $termRepo->findOneById($term);

        if (!$term) {
            throw $this->createNotFoundException("Term not found");
        }

        foreach ($user->getRegistrations() as $registration) {
            if ($registration->getTerm()==$term) {
                // just silent redirect, maybe exception or at least a flash?
                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $user->getId())));
            }
        }

        $registrationType = $this->container->get($term->getUsertype());

        $inst = $registrationType->getRegistrationEntityInstance();
        $inst->setUser($user);
        $inst->setTerm($term);
        $form = $this->createForm($registrationType->getRegistrationFormInstance(), $inst);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $em->persist($form->getData());

                $em->flush();

                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $user->getId())));
            }
        }

        return array(
            'user' => $user,
            'term' => $term,
            'form' => $form->createView(),
            'formpart' => $registrationType->getRegistrationFormPartial(),
        );
    }

    /**
     * Edits a registration
     * 
     * @param int $user user id
     * @param int $term term id
     * 
     * @return array template parameters
     *
     * @Route("/editreg/{user}/{term}", name = "admin_user_editreg" )
     * @Template
     */
    public function editRegistrationAction($user, $term)
    {
        if (!is_numeric($user)) {
            throw $this->createNotFoundException("User not found");
        }

        if (!is_numeric($term)) {
            throw $this->createNotFoundException("Term not found");
        }

        $em = $this->getDoctrine()->getEntityManager();
        $userRepo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:User')
            ;

        $user = $userRepo->findOneById($user);

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        $termRepo = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ;

        $term = $termRepo->findOneById($term);

        if (!$term) {
            throw $this->createNotFoundException("Term not found");
        }

        $inst = null;
        foreach ($user->getRegistrations() as $registration) {
            if ($registration->getTerm()==$term) {
                $inst = $registration;
            }
        }

        if (null===$inst) {
            throw $this->createNotFoundException("Regisration not found!");
        }

        $registrationType = $this->container->get($term->getUsertype());

        $form = $this->createForm($registrationType->getRegistrationFormInstance(), $inst);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $em->persist($form->getData());

                $em->flush();

                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $user->getId())));
            }
        }

        return array(
            'user' => $user,
            'term' => $term,
            'form' => $form->createView(),
            'formpart' => $registrationType->getRegistrationFormPartial(),
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

        $terms = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findAll() // todo: findUserNotIn($user)
            ;

        if (!$user) {
            throw $this->createNotFoundException("User not found");
        }

        return array(
            'user' => $user,
            'terms' => $terms,
        );
    }

}
