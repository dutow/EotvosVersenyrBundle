<?php

namespace Eotvos\VersenyrBundle\UserType;

use Eotvos\VersenyrBundle\Entity\Registration;
use Eotvos\VersenyrBundle\Form\Type\RegistrationType;

/**
 * Empty usertype.
 * 
 * @uses UserTypeInterface
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class DummyType implements UserTypeInterface
{
    /**
     * Returns an instance of the FormType of term registration.
     *
     * FormType should extend RegistrationType.
     * 
     * @return RegistrationType
     */
    public function getRegistrationFormInstance()
    {
        return new RegistrationType();
    }

    /**
     * Returns an instance of the term Registration entity
     *
     * FormType should extend RegistrationType.
     * 
     * @return Registration
     */
    public function getRegistrationEntityInstance()
    {
        return new Registration();
    }

    /**
     * Returns the partial twig template required by additional form parameters.
     * 
     * @return string
     */
    public function getRegistrationFormPartial()
    {
        return 'EotvosVersenyrBundle:User:emptyRegistration.html.twig';
    }

    /**
     * Returns the partial twig template rendering the right part of the registration page.
     * 
     * @return string
     */
    public function getRegistrationRightBox()
    {
        return 'EotvosVersenyrBundle:User:emptyRegistrationBox.html.twig';
    }
}

