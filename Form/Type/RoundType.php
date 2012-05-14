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
class RoundType extends AbstractType
{

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Builds the form.
     *
     * Symfony2 doesn't let us see the form data in this method, unless using an event listener hook. Which seems
     * perfectyl logical, but we need it. So we are requiring the calling method to give us the neccessary data in
     * the constructor...
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
        $roundtypes = $this->container->get('eotvos.versenyr.roundregistry')->getModuleList();
        $types = array();
        foreach ($roundtypes as $service => $obj) {
            $types[$service] = $obj->getDisplayName();
        }

        $resulttypes = $this->container->get('eotvos.versenyr.resultregistry')->getModuleList();
        $results = array();
        foreach ($resulttypes as $service => $obj) {
            $results[$service] = $obj->getName();
        }


        $builder->add('start', 'date', array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'));
        $builder->add('stop', 'date', array('widget' => 'single_text', 'format' => 'yyyy-MM-dd'));
        $builder->add('advanceNo', 'number');
        $builder->add('publicity', 'choice', array(
            'choices' => $results,
            'required' => true,
            'multiple' => false,
            'expanded' => false,
        ));

        $builder->add('roundtype', 'choice', array(
            'choices' => $types,
            'required' => true,
            'multiple' => false,
            'expanded' => false,
        ));
    }

    /**
     * Returns the name of the form type.
     * 
     * @return string
     */
    public function getName()
    {
        return 'section_form';
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
            'data_class' => 'Eotvos\VersenyrBundle\Entity\Round',
        );
    }

}

