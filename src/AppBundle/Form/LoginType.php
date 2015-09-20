<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('secret')
            ->setRequired(false)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'login';
    }
}
