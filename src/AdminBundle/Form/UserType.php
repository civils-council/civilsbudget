<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inn', null, array('label' => 'Идентифiкацiйний код'))
            ->add('numberBlank', null, array('label' => 'Номер бланка'))
            ->add('middleName', null, array('label' => 'Призвiще'))
            ->add('firstName', null, array('label' => 'Им\'я'))
            ->add('lastName', null, array('label' => 'По батьковi'))
            ->add('birthday', null, array('label' => 'Дата Народження'))
            ->add('sex', ChoiceType::class, array('label' => 'Gender',
                'choices' => array('Чоловік' => 'M', 'Жінка' => 'F'), ))
            ->add('phone')
            ->add('email');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false,
//            'validation_groups' => ['admin_user_post']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'adminbundle_user';
    }
}
