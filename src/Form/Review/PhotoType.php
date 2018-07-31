<?php

namespace App\Form\Review;

use App\Entity\Review\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;;
use Symfony\Component\Validator\Constraints\File;

class PhotoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Review\Photo'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }


//
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $builder
//            ->add('file', FileType::class, array(
//                'label' 	=> false,
//                'required' 	=> true,
//                'constraints' => array(
//                    new File(),
//                ),
//            ))
//            ->add('photo', FileType::class, [
//                'required' => false,
//                'label' => 'Логотип компании'
//            ]);
//
//    }
//
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => ReviewPhoto::class,
//        ]);
//    }
}
