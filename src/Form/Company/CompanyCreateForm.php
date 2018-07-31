<?php

namespace App\Form\Company;

use App\Entity\Company\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyCreateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название компании'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Контактный телефон'
            ])
            ->add('site', UrlType::class, [
                'label' => 'Сайт компании'
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'label' => 'Логотип компании'
            ])
            ->add('address', TextType::class, [
                'label' => 'Адрес',
                'required' => false,
            ])
            ->add('startWork', TimeType::class, [
                'label' => 'Начало работы'
            ])
            ->add('endWork', TimeType::class, [
                'label' => 'Окончание работы'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
