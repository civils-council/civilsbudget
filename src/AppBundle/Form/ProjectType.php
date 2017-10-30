<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Назва проекту'])
            ->add('description', TextareaType::class, [
                'label' => 'Про проект',
                'attr' => ['class' => 'form-control',
                    'rows' => '10']
            ])
            // TODO check email/http/name ???
            ->add('source', EmailType::class, ['label' => 'Відповідальна особа'])
            ->add('charge', NumberType::class, ['label' => 'Бюджет']);

        if (in_array('admin', $options) && $options['admin']) {
            $builder
                ->add('voteSetting', null, [
                    'label' => 'налаштування голосування'
                ])
                ->add('approved', ChoiceType::class, [
                'choices' => [
                    'Оприлюднити' => true,
                    'Блокувати' => false
                ],
                'choices_as_values' => true,
            ]);
        } else {
            $builder->add('picture', FileType::class, ['label' => 'Файл']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'admin' => false,
            'data_class' => 'AppBundle\Entity\Project',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project';
    }
}