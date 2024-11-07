<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\EqualTo;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Current Password',
                'mapped' => false, // This makes sure it's not mapped to the User entity
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your current password.']),
                ],
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'New Password',
                'mapped' => false, // Not mapped to User entity either
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your new password.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                    ]),
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirm New Password',
                'mapped' => false, // Same as above
                'constraints' => [
                    new NotBlank(['message' => 'Please confirm your new password.']),
                    new EqualTo([
                        'propertyPath' => 'new_password',
                        'message' => 'The password confirmation does not match the new password.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier le mot de passe',
                'attr' => [
                    'class' => 'btn-custom', // Ajouter des classes CSS pour le bouton
                ],
            ]);
         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
