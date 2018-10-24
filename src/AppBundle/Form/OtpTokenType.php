<?php

declare(strict_types=1);

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class OtpTokenType.
 */
class OtpTokenType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('token', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Обов\'язкове поле'])]
            ])
            ->add('permission', CheckboxType::class, [
                'label' => 'Даю дозвіл сервісу "Народна Рада" на обробку моїх персональних данних',
                'required' => true,
                'constraints' => [new NotBlank(['message' => 'Обов\'язкове поле'])]
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
