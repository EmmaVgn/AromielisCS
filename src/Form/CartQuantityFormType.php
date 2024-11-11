<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CartQuantityFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'data' => $options['quantity'], // Initialiser avec la quantité actuelle
                'attr' => ['min' => 1, 'max' => 100], // Restriction de la quantité
            ]);
    }
}