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
class ConfigurationType extends AbstractType
{

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Builds the form.
     * 
     * @param FormBuilder $builder builder
     * @param array       $options form options
     * 
     * @return void
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('value', 'text');

    }

    /**
     * Returns the name of the form type.
     * 
     * @return string
     */
    public function getName()
    {
        return 'configuration_form';
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
            'data_class' => 'Eotvos\VersenyrBundle\Entity\Configuration',
        );
    }

}

