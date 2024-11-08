<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Entity\User; // Ensure this is the correct namespace for your User entity

class AccountController extends AbstractController
{
    #[Route('/compte', name: 'account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
        ]);
    }

        /**
     * Permet la modification du mot de passe d'un utilisateur sur une page dédiée
     */
    #[Route('/compte/mot-de-passe', name: 'account_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, UserRepository $user): Response
    {
        $user->getUser();

        // Check if the user implements PasswordAuthenticatedUserInterface
        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new \LogicException('The user does not implement PasswordAuthenticatedUserInterface');
        }

        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('old_password')->getData();
            $newPassword = $form->get('new_password')->getData();

            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                if ($user instanceof User) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                } else {
                    throw new \LogicException('The user entity does not support password setting.');
                }
                $em->flush();
                $this->addFlash('success', 'Mot de passe modifié :)');
                return $this->redirectToRoute('account');
            } else {
                $this->addFlash('error', 'Mot de passe actuel erroné :(');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
