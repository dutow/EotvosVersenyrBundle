<?php

namespace Eotvos\VersenyrBundle\UserType;

/**
 * Interface for user registration providers. 
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
interface UserTypeInterface
{
    /**
     * Returns an instance of the FormType of term registration.
     *
     * FormType should extend RegistrationType.
     * 
     * @return RegistrationType
     */
    public function getRegistrationFormInstance();

    /**
     * Returns an instance of the term Registration entity
     *
     * FormType should extend RegistrationType.
     * 
     * @return Registration
     */
    public function getRegistrationEntityInstance();

    /**
     * Returns the partial twig template required by additional form parameters.
     * 
     * @return string
     */
    public function getRegistrationFormPartial();

    /**
     * Returns the partial twig template rendering the right part of the registration page.
     * 
     * @return void
     */
    public function getRegistrationRightBox();

    public function getDisplayName();
}




