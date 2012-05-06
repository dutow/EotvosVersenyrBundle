<?php

namespace Eotvos\VersenyrBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Service for random password generation and encoding.
 *
 * @uses ContainerAware
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class PasswordGenerator extends ContainerAware
{

    /**
     * Generates a new random string with given length and characters
     *
     * @param string $length string length
     * @param string $chars  allowed characters
     *
     * @return string the random string
     */
    public function generateRandomString($length, $chars="234567890abcdefghijkmnoprstuvwxABCDEFGHIJKLMNOPRSTUVWX")
    {
        $actPos = 0;
        $string = "";
        while ($actPos <= $length) {
            $string .= $chars{mt_rand(0, strlen($chars)-1)};
            $actPos++;
        }

        return $string;
    }

    /**
     * Generates a new random password with the given lengths to the user.
     *
     * @param UserInterface &$userObject target User
     * @param string        $length      password length
     * @param string        $saltLength  salt length
     * @param string        $chars       allowed characters
     *
     * @return string the new password
     */
    public function generatePassword(
        &$userObject,
        $length=8,
        $saltLength=4,
        $chars="234567890abcdefghijkmnoprstuvwxABCDEFGHIJKLMNOPRSTUVWX"
        )
    {
        $password = $this->generateRandomString($length, $chars);
        $salt = $this->generateRandomString($saltLength, $chars);

        $this->encodePassword($userObject, $password, $salt);

        return $password;
    }

    /**
     * Encodes a password and salt into a user object.
     *
     * @param UserInterface &$userObject target User
     * @param string        $password    password
     * @param string        $salt        salt
     *
     * @return boolean true if successful
     */
    public function encodePassword(&$userObject, $password, $salt='')
    {
        if ($salt=='') {
            $chars="234567890abcdefghijkmnoprstuvwxABCDEFGHIJKLMNOPRSTUVWX";
            $salt = $this->generateRandomString(8, $chars);
        }
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($userObject);
        $userObject->setSalt($salt);
        $userObject->setPassword($encoder->encodePassword($password, $salt));

        return true;
    }

}
