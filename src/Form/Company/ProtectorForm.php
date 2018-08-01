<?php

namespace App\Form\Company;

use App\Entity\Company\Protector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProtectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label' => 'Вопрос'
            ])
            ->add('correctOption', TextType::class, [
                'label' => 'Корректный ответ'
            ])
            ->add('wrongOptionFirst', TextType::class, [
                'label' => 'Неверный вариант ответа 1'
            ])
            ->add('wrongOptionSecond', TextType::class, [
                'label' => 'Неверный вариант ответа 2'
            ])
            ->add('wrongOptionThird', TextType::class, [
                'label' => 'Неверный вариант ответа 3'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Protector::class,
        ]);
    }
}
