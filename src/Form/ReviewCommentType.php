<?php

namespace App\Form;

use App\Entity\ReviewComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text')
            ->add('isCompany')
//            ->add('status')
//            ->add('lft')
//            ->add('lvl')
//            ->add('rgt')
//            ->add('root')
//            ->add('created')
//            ->add('updated')
//            ->add('review')
//            ->add('user')
//            ->add('parent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReviewComment::class,
        ]);
    }
}
