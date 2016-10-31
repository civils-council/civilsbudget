<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numberBlank', null, array('label' => 'Номер бланка'))
            ->add('middleName', null, array('label' => 'Призвiще'))
            ->add('firstName', null, array('label' => 'Имя'))
            ->add('lastName', null, array('label' => 'По батьковi'))
            ->add('birthday', null, array('label' => 'Дата Народження'))
            ->add('inn', null, array('label' => 'Идентифiкацiйний код'))
            ->add('sex', 'choice', array('label' => 'Gender', 'max_length' => 255,
                'choices' => array('М' => 'Чоловік', 'Ж' => 'Жінка'), ))
            ->add('phone')
            ->add('email');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false,
//            'validation_groups' => ['admin_user_post']
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'adminbundle_user';
    }
}
