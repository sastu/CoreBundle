<?php

namespace Core\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Core\Bundle\CoreBundle\Entity\NewsletterShipping;

/**
 * Class NewsletterShippingType
 */
class NewsletterShippingType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newsletter', 'entity', array(
                'class' => 'CoreBundle:Newsletter',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c');
//                        ->where('c.parentCategory IS NOT NULL');
                },
                'required' => false
            ))
            ->add('type', 'choice', array(
                    'label' => 'newsletter.shipping.type',
                    'choices' => array(
                         NewsletterShipping::TYPE_ALL => 'Enviar a todos los suscriptores',
                         NewsletterShipping::TYPE_MANAGER => 'Enviar a los managers',
                         NewsletterShipping::TYPE_INVESTOR => 'Enviar a los inversores',
                         NewsletterShipping::TYPE_USER => 'Enviar a los usuarios normales',
                    )
                )
            )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\CoreBundle\Entity\NewsletterShipping',
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'core_corebundle_newslettershippingtype';
    }
}
