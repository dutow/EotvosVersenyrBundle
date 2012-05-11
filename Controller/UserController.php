<?php

namespace Eotvos\VersenyrBundle\Controller;

use Eotvos\VersenyrBundle\Entity as Entity;

use Eotvos\VersenyrBundle\Form\Type as FormType;

use Eotvos\VersenyrBundle\UserType\DummyType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;


/**
 * Controller for user related tasks.
 *
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class UserController extends Controller
{
    /**
     * Login request or failure display
     *
     * @return array template parameters
     *
     * @Route("/logged_in", name="loggedin_page")
     */
    public function loggedInAction()
    {
        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->getLastTerm()
            ;

        return $this->redirect($this->generateUrl('archives'));
    }

    /**
     * Login request or failure display
     *
     * @return array template parameters
     *
     * @todo display login request
     * @todo better design
     *
     * @Route("/login", name="login_page")
     * @Template()
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->getLastTerm()
            ;

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
            'term'          => $term,
        );
    }

    /**
     * Displas a message after a successful registration.
     *
     * @param int $term target term
     *
     * @return array template parameters
     *
     * @todo get slug from configuration
     * @todo add check for previous user registration with session ?
     *
     * @Route("/{term}/jelentkezes_sikeres", name="competition_register_success" )
     * Cache(expires="+7 days")
     * @Template()
     */
    public function regsuccessAction($term)
    {

        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $pageRec = $tpRep->getForTermWithSpecial($term, 'register_after');

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

        return array(
            'page' => $pageRec,
            'term' => $term,
        );
    }

    /**
     * Registers a new user.
     *
     * @return array template parameters
     *
     * @Route("/{term}/registration", name="competition_register" )
     * @Template()
     */
    public function registerAction($term)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        if (!$term->getActive()) {
            throw $this->createNotFoundException('Term not found');
        }

        $sec = $this->get('security.context');
        $curruser = $sec->getToken()->getUser();

        if ($sec->isGranted('ROLE_USER') && $curruser->getRegistrationForTerm($term)) {
            $rec = $this->getDoctrine()
                ->getRepository('EotvosVersenyrBundle:Textpage')
                ->getForTermWithSpecial($term->getName(), 'register_done')
                ;

            return $this->redirect($this->generateUrl('competition_page', array( 'term' => $term->getName(), 'pageSlug' => $rec->getSlug())));
        }

        if (!$term->open()) {
            $rec = $this->getDoctrine()
                ->getRepository('EotvosVersenyrBundle:Textpage')
                ->getForTermWithSpecial($term->getName(), 'register_notopen')
                ;

            return $this->redirect($this->generateUrl('competition_page', array( 'term' => $term->getName(), 'pageSlug' => $rec->getSlug())));
        }

        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');

        $userType = $this->container->get($term->getUsertype());

        $user = new Entity\User();
        $registration = $userType->getRegistrationEntityInstance();
        $registration->setUser($user);

        $registration->setTerm($term);

        $user->addRegistration($registration);

        $form = $this->createForm(new FormType\UserregType($userType->getRegistrationFormInstance()));

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);

            if ($form->isValid()) {
                $ur = $form->getData();
                $user = $ur['user'];
                $registration = $ur['registration'];
                $user->addRegistration($registration);

                $password = $user->getPassword();
                $passwordGenerator = $this->get('eotvos.versenyr.password_generator');
                $passwordGenerator->encodePassword($user, $user->getPassword());

                $em->persist($registration);
                $user->addRegistration($registration);
                $em->persist($user);
                $em->flush();
                $registration->setUser($user);
                $registration->setTerm($term);
                $em->flush();

                $userMailer = $this->get('eotvos.versenyr.mailer.user');
                $userMailer->sendRegistrationNotification($term, $user, $password);
                $userMailer->sendRegistrationAdminMessages($term, $user);

                return $this->redirect($this->generateUrl('competition_register_success', array('term' => $term->getName())));
            }
        }

        $formView = $form->createView();
        // dirty hack, but according to symfony docs, this is the best...
        // http://stackoverflow.com/questions/8012718/symfony2-form-repeated-element-custom-labels
        $formView->getChild('user')->getChild('password')->getChild('second')->set('label', 'registration.form.confirm');

        return array(
            //'page' => $pageRec,
            'form' => $formView,
            'subform' => $userType->getRegistrationFormPartial(),
            'rightpart' => $userType->getRegistrationRightBox(),
            'term' => $term,
        );
    }

    /**
     * Generates an overview about the current user
     *
     * @return array empty template parameters
     *
     * @Route("/{term}/felhasznalo/profil", name="competition_profile" )
     * @Template()
     */
    public function profileAction($term)
    {
        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        return array(
            'term' => $term,
        );
    }

    /**
     * Generates an overview table of the competition for the user
     *
     * @param int $term target term
     *
     * @return array template parametrs
     *
     * @Route("/{term}/felhasznalo/szekciok", name="competition_subscriptions" )
     * @Template()
     */
    public function subscriptionsAction($term)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;

        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        $pageRec = $tpRep->getForTermWithSpecial($term->getName(), 'register');

        // TODO: not logged in
        // TODO: move tu user controller
        if (!$pageRec) {
            throw $this->createNotFoundException("Sections root not found");
        }

        $secRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:Section');
        $sections = $secRep->getForTerm($term->getName());

        $sec = $this->get('security.context');
        $curruser = $sec->getToken()->getUser();

        $registration = $curruser->getRegistrationForTerm($term);

        return array(
            'page' => $pageRec,
            'sections' => $sections,
            'term' => $term,
            'registration' => $registration,
        );
    }


    /**
     * Action for a user to register on a previously not selected section.
     *
     * @param int    $term    target term
     * @param string $section target section
     *
     * @return array template parameters
     *
     * @todo better messages
     *
     * @Route("/{term}/felhasznalo/feliratkozas/{section}", name="competition_subscribe" )
     * @Template()
     */
    public function subscribeAction($term, $section)
    {
        $tpRep = $this->getDoctrine()->getRepository('\EotvosVersenyrBundle:TextPage');
        $record = $tpRep->getForTermWithSlug($term, $section);

        $term = $this->getDoctrine()
            ->getRepository('EotvosVersenyrBundle:Term')
            ->findOneByName($term)
            ;
        if (!$term) {
            throw $this->createNotFoundException('Term not found');
        }

        if (!$record || $record->getSection()==null) {
            $this->get('session')->setFlash('error', "Jelentkezesi hiba!");
        } else {
            $user = $this->get('security.context')->getToken()->getUser();

            if (!$user->getRegistrationForTerm($term)->mayJoin($record->getSection())) {
                $this->get('session')->setFlash('error', "Jelentkezesi hiba!");
            } else {
                $registration = $user->getRegistrationForTerm($term);
                $registration->addSection($record->getSection());

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($registration);
                $em->flush();

                $this->get('session')->setFlash('info', "Sikeresen jelentkeztél a szekcióba!");
            }
        }

        return $this->redirect($this->generateUrl('competition_subscriptions', array('term' => $term->getName())));
    }
}
