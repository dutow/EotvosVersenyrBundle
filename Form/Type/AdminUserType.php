<?php

namespace Eotvos\VersenyrBundle\Form\Type;

use Eotvos\VersenyrBundle\Entity as Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form type for user registration. Data modification is handled by different forms.
 *
 * @uses AbstractType
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class AdminUserType extends AbstractType
{

    /**
     * __construct
     * 
     * @return void
     */
    public function __construct()
    {
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
        $builder->add(
            'email',
            'email',
            array(
                'label' => 'registration.form.email',
            ))
            ;

        $builder->add(
            'password',
            'repeated',
            array(
                'type' => 'password',
                'invalid_message' => 'registration.error.nomatch',
                'required' => true,
                'options' => array(
                    'label' => 'registration.form.password',
                ),
            ))
            ;

        $builder->add(
            'admin',
            'checkbox'
        );

        $builder->add(
            'tester',
            'checkbox'
        );

    }

    /**
     * Returns the name of the form type.
     * 
     * @return string
     */
    public function getName()
    {
        return 'user_registration';
    }

    /**
     * Default options.
     * 
     * @param array $options form options
     * 
     * @return array options
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Eotvos\VersenyrBundle\Entity\User',
        );
    }

}


