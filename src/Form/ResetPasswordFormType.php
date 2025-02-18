<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Merci de saisir votre mot de passe',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ý0-9]).{12,}$/',
                        'message' => 'Pour des raisons de sécurité, votre mot de passe doit contenir au minimum 12 caractères, dont 1 lettre majuscule, 1 lettre minuscule, 1 chiffre et 1 caractère spécial.',
                    ]),
                ],
                'first_options' => [
                    'label' => 'Votre mot de passe :',
                    'label_attr' => [
                        'class' => 'lh-label fw-medium',
                    ],
                    'attr' => [
                        'placeholder' => 'Merci de saisir votre mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe :',
                    'label_attr' => [
                        'class' => 'lh-label fw-medium',
                    ],
                    'attr' => [
                        'placeholder' => 'Merci de confirmer votre mot de passe',
                    ],
                ],
            ])
            ->add('_token', HiddenType::class, [
                'mapped' => false,
                'data' => $options['csrf_token'], // Utilisation de la valeur du token
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_token' => null, // Ajout de l'option csrf_token
        ]);
    }
}
