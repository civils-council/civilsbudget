<?php

declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Helper\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class VoterType.
 */
class VoterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inn', TextType::class, [
                'label' => 'Ідентифiкацiйний код',
                'disabled' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
                'attr' => ['placeholder' => 'Формат: +380999999999', 'mask' => UserManager::PHONE_PATTERN],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return null;
    }

}