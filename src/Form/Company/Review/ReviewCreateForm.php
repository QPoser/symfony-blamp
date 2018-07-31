<?php

namespace App\Form\Company\Review;

use App\Entity\Review\Review;
use App\Form\Review\PhotoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewCreateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Отзыв',
            ])
            ->add('assessment', ChoiceType::class, [
                'choices' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                ],
                'label' => 'Ваша оценка'
            ])
            ->add('photos', CollectionType::class,array(
                'entry_type' => PhotoType::class,
                'allow_add' => true,
                'by_reference' => false,
            ))
            ;










//            ->add('photos', FileType::class, [
//                'multiple' => true,
//                'attr'     => [
//                    'accept' => 'image/*',
//                    'multiple' => 'multiple'
//                ]
//            ])
//        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
