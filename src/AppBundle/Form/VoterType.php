<?php

declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Helper\UserManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('lastName', TextType::class, ['label' => 'Прізвище'])
            ->add('firstName', TextType::class, ['label' => 'Ім\'я'])
            ->add('middleName', TextType::class, ['label' => 'По-батьковi'])
            ->add('birthday', TextType::class, [
                'label' => 'Дата Народження',
                'attr' => ['placeholder' => 'Формат: 1950-05-25, якщо є ІНН, Дата Народження буде вирахувано автоматично']
            ])
            ->add('sex', ChoiceType::class, [
                'label' => 'Стать',
                'choices' => ['Чоловік' => 'M', 'Жінка' => 'F'],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
                'attr' => ['placeholder' => 'Формат: +380999999999', 'mask' => UserManager::PHONE_PATTERN],
            ])
            ->add('withPolicy', CheckboxType::class, [
                'required' => false,
                'label' => 'Вислати смс з посиланням про деталі обробки моїх персональних данних'
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