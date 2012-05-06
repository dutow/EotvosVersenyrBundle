<?php

namespace Eotvos\VersenyrBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Comways\Component\Form\RecaptchaField;

/**
 * Validates if a collection has at least one element. 
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class EntityCollectionValidator extends \Symfony\Component\Validator\ConstraintValidator
{

    /**
     * A collection is valid if it is not empty.
     * 
     * @param Value      $value      value of the field
     * @param Constraint $constraint form constraints
     * 
     * @return void
     *
     * @todo make more generic
     */
    public function isValid($value, Constraint $constraint)
    {
        if ($value->count()<1) {
            $this->setMessage($constraint->message, array('{{ field }}' => $constraint->field));

            return false;
        }

        return true;
    }

}

