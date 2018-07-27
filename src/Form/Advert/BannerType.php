<?php

namespace App\Form\Advert;

use App\Entity\Advert\Banner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('src')
            ->add('bannerImg', FileType::class)
            ->add('format', ChoiceType::class, [
                'choices' => Banner::formatsList(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,

            'validation_groups' => function (FormInterface $form) {
                $data = $form['format']->getData();

                if (mb_strtolower($data) == Banner::FORMAT_VERTICAL) {
                    return ['vertical'];
                } elseif (mb_strtolower($data) == Banner::FORMAT_HORIZONTAL) {
                    return ['horizontal'];
                }

                return ['vertical'];
            }
        ]);
    }
}
