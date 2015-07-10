<?php

namespace Core\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SliderType
 */
class SliderType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('caption', null, array(
                'required' => false
            ))
            ->add('openInNewWindow', null, array(
                'required' => false
            ))
            ->add('url', 'url')
            ->add('active', null, array(
                'required' => false
            ))
            ->add('order')
            ->add('image', new ImageType(), array(
                'error_bubbling' => false,
                'required' => false
            ))
//            ->add('price', 'text', array(
//                'required' => false
//            ))
                ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' =>  'Core\Bundle\CoreBundle\Entity\Slider',
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_corebundle_slidertype';
    }
}
