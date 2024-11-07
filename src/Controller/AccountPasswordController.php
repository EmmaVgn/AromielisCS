<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    #[Route('/account/change-password', name: 'account_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $user = new User();  // Or fetch from the database if you're updating


        // Vérifier si l'utilisateur est authentifié et implémente PasswordAuthenticatedUserInterface
        if (!$user instanceof \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface) {
            throw new \LogicException('User is not authenticated or does not implement PasswordAuthenticatedUserInterface.');
        }

        // Créer le formulaire de changement de mot de passe
        $form = $this->createForm(ChangePasswordType::class, $user);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe en clair et le hacher
            $newPassword = $form->get('new_password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

            // Assigner le mot de passe haché à l'utilisateur
            $user->setPassword($hashedPassword);

            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Your password has been updated successfully.');

            // Rediriger l'utilisateur vers une page appropriée (par exemple, profil)
            return $this->redirectToRoute('account_profile');
        }

        // Rendu du formulaire dans la vue
        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
