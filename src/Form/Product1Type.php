<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class Product1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('keywords')
            ->add('summary')
            ->add('image', FileType::class, [
                'label' =>'Product Main Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes'=> [
                            'image/*',
                        ],

                        'mimeTypesMessage' => 'Please upload a valid Image File',
                    ])
                ],
            ])
            ->add('type')
            ->add('adres')
            ->add('sponsor')
            ->add('city', ChoiceType::class,[
                'choices' => [
                    'Karabuk' => 'Karabuk',
                    'Bursa' => 'Bursa',
                    'Madrid' => 'Madrid',
                    'Roma' => 'Roma',
                    'Belgrad' => 'Belgrad',
                    'London' => 'London'
                ],
            ])
            ->add('country', ChoiceType::class,[
                'choices' => [
                    'Turkey' => 'Turkey',
                    'Spain' => 'Spain',
                    'Italy' => 'Italy',
                    'Sirbia' => 'Sirbia',
                    'England' => 'England'
                ],
            ])
            ->add('detail', CKEditorType::class,array(
                'config' => array(
                    'uiColor' => '#ffffff',
                    //...
                ),
            ))

            ->add('created_at')
            ->add('updated_at')
            ->add('description')
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'title',
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
