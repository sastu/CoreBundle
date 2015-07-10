<?php

namespace Core\Bundle\CoreBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Core\Bundle\CoreBundle\Form\ImageType;

class ActorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surnames')
            ->add('username')
            ->add('password', 'password', array('required' => false))
            ->add('email')
            ->add('image', new ImageType(), array(
                'error_bubbling' => false,
                'required' => false
            ))
            ->add('isActive', 'checkbox', array('required' => false))

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\CoreBundle\Entity\Actor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_bundle_corebundle_actor';
    }
}
