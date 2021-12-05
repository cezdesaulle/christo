<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        if ($options['add'] == true): // si on est en ajout de produit c'est ce formulaire qui est utilisé

            $builder
                ->add('title', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        "placeholder" => 'saisir le nom du produit'
                    ]

                ])
                ->add('categorie',EntityType::class, [
                    "class"=>Categorie::class,
                    "choice_label"=>"nom"

                ])
                ->add('price', NumberType::class, [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'saisir le prix'
                    ]
                ])
                ->add('description', TextareaType::class, [

                    'required' => false,
                    'label' => false,
                ])
                ->add('picture', FileType::class, [
                    'required' => false,
                    'label' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg',
                            ],
                            'mimeTypesMessage' => "les extensions autorisées sont: PNG, JPG, JPEG"
                        ])
                    ]
                ])
                ->add('Valider', SubmitType::class);

        elseif ($options['update']==true):

            $builder
                ->add('title', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        "placeholder" => 'saisir le nom du produit'
                    ]

                ])
                ->add('price', NumberType::class, [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'saisir le prix'
                    ]
                ])
                ->add('categorie',EntityType::class, [
                    "class"=>Categorie::class,
                    "choice_label"=>"nom"

                ])
                ->add('description', TextareaType::class, [

                    'required' => false,
                    'label' => false,
                ])
                ->add('updatePicture', FileType::class, [
                    'required' => false,
                    'label' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg',
                            ],
                            'mimeTypesMessage' => "les extensions autorisées sont: PNG, JPG, JPEG"
                        ])
                    ]
                ])
                ->add('Valider', SubmitType::class);


        endif;


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'add'=>false,
            'update'=>false
        ]);
    }
}
