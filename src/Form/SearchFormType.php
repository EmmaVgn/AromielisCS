<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'All Categories' => null, // Option to filter all categories
                    'Category 1' => 'category1',
                    'Category 2' => 'category2',
                    // Add dynamic categories here using a repository or array
                ],
                'required' => false,
            ])
            ->add('minPrice', IntegerType::class, [
                'required' => false,
                'label' => 'Min Price',
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => 'Max Price',
            ])
            ->add('q', TextType::class, [
                'required' => false,
                'label' => 'Search Query',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
        ]);
    }
}
