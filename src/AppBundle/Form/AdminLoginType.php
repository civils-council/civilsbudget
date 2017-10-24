<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add(
                'password',
                PasswordType::class,
                [
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->setRequired(false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_login';
    }
}
