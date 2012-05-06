<?php

namespace Eotvos\VersenyrBundle\Mailer;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class for user related e-mails.
 * 
 * @uses ContainerAware
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class UserMailer extends ContainerAware
{

    /**
     * Renders a view.
     *
     * Copied from Controller.
     * 
     * @param View     $view       view
     * @param array    $parameters view parameters
     * @param Response $response   response object
     * 
     * @return string
     */
    protected function renderView($view, array $parameters = array(), Response $response = null)
    {
        return $this->container->get('templating')->renderResponse($view, $parameters, $response);
    }

    /**
     * Sends a message to the newly registered user.
     *
     * @param UserInterface $userObject registered object
     * @param string        $password   initial password
     *
     * @return void
     *
     * @todo make sender parameters and subject customizable
     */
    public function sendRegistrationNotification($userObject, $password)
    {
        $body = $this->renderView(
            'EotvosVersenyrBundle:User:email.txt.twig',
            array('user' => $userObject, 'password' => $password)
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('Eötvös József Tanulmányi Verseny Regisztráció')
            ->setFrom('verseny@eotvos.elte.hu')
            ->setTo($userObject->getEmail())
            ->setBody($body)
            ;

        $this->container->get('mailer')->send($message);
    }

    /**
     * Sends a message about the newly registered user to the administrators.
     *
     * Administrators are specified in the sections data, or in dev mode the config file.
     *
     * @param UserInterface $userObject newly registered user
     *
     * @return void
     *
     * @todo implement dev mode.
     * @todo refactor into one multi recipient bcc mail
     * @todo make recipients and subject customizable
     */
    public function sendRegistrationAdminMessages($userObject)
    {
        $body = $this->renderView('EotvosVersenyrBundle:User:email_belso.txt.twig', array('user' => $userObject));
        $message = \Swift_Message::newInstance()
            ->setSubject('Eötvös József Tanulmányi Verseny Regisztráció')
            ->setFrom('verseny@eotvos.elte.hu')
            ->setTo('eotvosversenyr@gmail.com')
            ->setBody($body)
            ;

        $this->container->get('mailer')->send($message);

        $body = $this->renderView('EotvosVersenyrBundle:User:email_belso.txt.twig', array('user' => $userObject));

        foreach ($userObject->getSections() as $sec) {
            $targets = json_decode($sec->getNotify());
            foreach ($targets as $key => $email) {
                $email = $email[1];
                $message = \Swift_Message::newInstance()
                    ->setSubject('Eötvös József Tanulmányi Verseny Regisztráció')
                    ->setFrom('verseny@eotvos.elte.hu')
                    ->setTo($email)
                    ->setBody($body)
                    ;
                $this->container->get('mailer')->send($message);
            }
        }
    }

}
