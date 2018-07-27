<?php

namespace App\Form\Company\Review;

use App\Entity\Review\Review;
use App\Form\Review\ReviewPhotoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewCreateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text')
            ->add('assessment', ChoiceType::class, [
                'choices' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                ]
            ]);
//            ->add('photos', CollectionType::class, array(
//                'entry_type'   		=> ReviewPhotoType::class,
//                'prototype'			=> true,
//                'allow_add'			=> true,
//                'allow_delete'		=> true,
//                'by_reference' 		=> false,
//                'required'			=> false,
//                'label'			=> false,
//
//            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
