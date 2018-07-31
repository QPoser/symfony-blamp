<?php

namespace App\Form\Company;

use App\Entity\Company\Protector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProtectorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('correctOption')
            ->add('wrongOptionFirst')
            ->add('wrongOptionSecond')
            ->add('wrongOptionThird')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Protector::class,
        ]);
    }
}
