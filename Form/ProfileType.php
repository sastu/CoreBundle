<?php

namespace Core\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Core\Bundle\CoreBundle\Entity\Actor;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * {@inheritDoc}
 */
class ProfileType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('name')
            ->add('surnames')
     ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\CoreBundle\Entity\Actor'
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_corebundle_profiletype';
    }
}