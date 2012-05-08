<?php

namespace Eotvos\VersenyrBundle\Form\Type;

use Eotvos\VersenyrBundle\Entity as Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form type for user registration. Data modification is handled by different forms.
 *
 * @uses AbstractType
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class UserregType extends AbstractType
{

    /**
     * Creates a new userreg form consisting of a user registration and a term registration part.
     * 
     * @param RegistrationType $registrationType term form type
     * 
     * @return void
     */
    public function __construct(RegistrationType $registrationType)
    {
        $this->termType = $registrationType;
    }


    /**
     * Builds the form.
     * 
     * @param FormBuilder $builder builder
     * @param array       $options form options
     * 
     * @return void
     *
     * @todo I18N
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('user', new UserType());
        $builder->add('registration', $this->termType);
    }

    /**
     * Returns the name of the form type.
     * 
     * @return string
     */
    public function getName()
    {
        return 'userreg';
    }

}

