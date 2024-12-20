<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CartConfirmationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('fullName', TextType::class, [
            'label' => 'Nom complet',
            'attr' => [
                'placeholder' => 'Nom complet pour la livraison'
            ]
        ])
        ->add('adress', TextareaType::class, [
            'label' => 'Adresse complete',
            'attr' => [
                'placeholder' => 'Adresse complète pour la livraison'
            ]
        ])
        ->add('postalCode', TextType::class, [
            'label' => 'Code postal',
            'attr' => [
                'placeholder' => 'Code postal pour la livraison'
            ]
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'attr' => [
                'placeholder' => 'Ville pour la livraison'
            ]
        ]);
    }


}