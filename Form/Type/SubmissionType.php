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
class SubmissionType extends AbstractType
{

    public function __construct(ContainerInterface $container, Entity\Round $round)
    {
        $this->container = $container;
        $this->round = $round;
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
        $users = $this
            ->container
            ->get('doctrine')
            ->getRepository('EotvosVersenyrBundle:User')
            ->getActiveForRound($this->round)
            ;

        $builder->add('user', 'entity', array(
            'class' => 'Eotvos\VersenyrBundle\Entity\User',
            'choices' => $users,
            'multiple' => false,
            'expanded' => false,
            'required' => true,
        ));

        $builder->add('advances', 'choice', array(
            'choices' => array(1 => 'NEM', 2 => 'IGEN'),
            'multiple' => false,
            'expanded' => false,
            'required' => false
        ));

        $builder->add('category');
        $builder->add('points', 'number');
    }

    /**
     * Returns the name of the form type.
     * 
     * @return string
     */
    public function getName()
    {
        return 'submission_form';
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
            'data_class' => 'Eotvos\VersenyrBundle\Entity\Submission',
        );
    }

}

