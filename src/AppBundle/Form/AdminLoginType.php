<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', 'password')
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
