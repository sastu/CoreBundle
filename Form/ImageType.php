<?php

namespace Core\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ImageType
 */
class ImageType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', null, array(
                'label' => 'image.singular',
            ));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\CoreBundle\Entity\Image'
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_corebundle_imagetype';
    }
}
