<?php

namespace Eotvos\VersenyrBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Annotation for the EntityCollectionValidator
 *
 * @Annotation
 * 
 * @uses Constraint
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 *
 * @todo documentation
 */
class EntityCollection extends Constraint
{
    public $message = 'Error hehe';
    public $field;

    /**
     * validates properties
     * 
     * @return void
     */
    public function targets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

}
