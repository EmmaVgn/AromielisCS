<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => 'Current Password',
                'mapped' => false, // This makes sure it's not mapped to the User entity
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'New Password',
                'mapped' => false, // Not mapped to User entity either
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirm New Password',
                'mapped' => false, // Same as above
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Changer le mot de passe',
                'attr' => ['class' => 'btn btn-custom'] // Classe CSS personnalisée
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
