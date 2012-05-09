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
class TextPageType extends AbstractType
{

    public function __construct(ContainerInterface $container, $special = null, $subform = null)
    {
        $this->container = $container;
        $this->special = $special;
        $this->subform = $subform;
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


        $builder->add('title', 'text');
        $builder->add('body', 'textarea');

        if (!$this->special) {
            $builder->add('inMenu', 'checkbox');
            $builder->add('fbbox', 'checkbox');

            $parentlist = $this
                ->container
                ->get('doctrine')
                ->getRepository('EotvosVersenyrBundle:TextPage')
                ->getPossibleParentList();
            $parent = array();
            foreach ($parentlist as $record) {
                $title = $record->getTitle();
                for ($i = 0; $i < $record->getLvl(); $i++) {
                    $title = '-'.$title;
                }
                $parents[$record->getId()] = $title;
            }

            $builder->add('parent', 'entity', array(
                'class' => 'Eotvos\VersenyrBundle\Entity\TextPage',
                'property' => 'titleWithLevel',
                'required' => true,
                'choices' => $parentlist,
                'multiple' => false,
                'expanded' => false,
            ));
        } else {
            if ($this->subform) {
                $builder->add($this->special, $this->subform);
            }
        }

    }

    /**
     * Returns the name of the form type.
     * 
     * @return string
     */
    public function getName()
    {
        return 'textpage_form';
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
            'data_class' => 'Eotvos\VersenyrBundle\Entity\TextPage',
        );
    }

}

