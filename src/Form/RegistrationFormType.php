<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CsrfTokenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'required' => true,
                'attr' => ['placeholder' => 'Merci de saisir votre adresse email'],
                'constraints' => [
                    new Email(['message' => 'Veuillez entrer une adresse email valide.'])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation.'
                    ]),
                ],
            ])
            // Ajout de la case à cocher "J'accepte de recevoir des mails commerciaux"
            ->add('receivePromotions', CheckboxType::class, [
                'mapped' => false,  // Ne pas lier ce champ à l'entité User directement
                'label' => 'J\'accepte de recevoir des mails commerciaux.',
                'required' => false,  // Rendre optionnel ou obligatoire selon votre besoin
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Merci de saisir votre mot de passe'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
                'first_options' => [
                    'label' => 'Votre mot de passe :',
                    'label_attr' => ['class' => 'lh-label fw-medium'],
                    'attr' => [
                        'placeholder' => 'Merci de saisir votre mot de passe',
                        'class' => 'form-control',
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe :',
                    'label_attr' => ['class' => 'lh-label fw-medium'],
                    'attr' => [
                        'placeholder' => 'Merci de confirmer votre mot de passe',
                        'class' => 'form-control',
                    ]
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'attr' => ['placeholder' => 'Merci de saisir votre prénom'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 30,
                        'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Votre prénom ne peut excéder {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'attr' => ['placeholder' => 'Merci de saisir votre nom'],
                'constraints' => new NotBlank(['message' => 'Merci d\'indiquer votre nom.'])
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre adresse :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'attr' => ['placeholder' => 'Merci de saisir votre adresse'],
                'constraints' => new NotBlank(['message' => 'Merci d\'indiquer votre adresse.'])
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Votre code postal :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'attr' => ['placeholder' => 'Merci de saisir votre code postal'],
                'constraints' => new NotBlank(['message' => 'Merci d\'indiquer votre code postal.'])
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre ville :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'attr' => ['placeholder' => 'Merci de saisir votre ville'],
                'constraints' => new NotBlank(['message' => 'Merci d\'indiquer votre ville.'])
            ])
            ->add('phone', TextType::class, [
                'label' => 'Votre téléphone :',
                'label_attr' => ['class' => 'lh-label fw-medium'],
                'attr' => ['placeholder' => 'Merci de saisir votre téléphone'],
                'constraints' => [
                    new Regex([
                        'pattern' => "/^(\+[0-9]{2}[.\-\s]?|00[.\-\s]?[0-9]{2}|0)([0-9]{1,3}[.\-\s]?(?:[0-9]{2}[.\-\s]?){4})$/",
                        'message' => "Le numéro de téléphone '{{ value }}' n'est pas valide."
                    ]),
                    new NotBlank(['message' => 'Merci d\'indiquer votre numéro de téléphone.'])
                ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
