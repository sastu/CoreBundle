<?php

namespace Core\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Core\Bundle\CoreBundle\Form\ImageType;

/**
 * Class ActorEditType
 */
class ActorEditType extends AbstractType
{
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\CoreBundle\Entity\Actor',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_corebundle_actoredittype';
    }
}
