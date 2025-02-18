<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Service\SendMailService;
use HTMLPurifier;
use HTMLPurifier_Config;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('account');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'security_forgotPw')]
    public function forgotPw(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $em, SendMailService $mail): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy([
                'email' => $form->get('email')->getData()
            ]);
            if ($user) {
                // On génère un token de réinitialisation
                $token = $tokenGeneratorInterface->generateToken();
                $now = new DateTimeImmutable();
                $user->setResetToken($token);
                $user->setCreatedTokenAt($now);
                $em->flush();
                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('security_resetPw', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                // On crée les données du mail
                $context = [
                    'url' => $url,
                    'user' => $user
                ];
                // Envoi du mail
                $mail->sendEmail(
                    'contact@mariefarjaud.fr',
                    'Renitialisation du mot de passe chez Aromielis',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );
                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/reset_password_request.html.twig', [
            'formView' => $form->createView(),
        ]);
    }

    #[Route('/forgot-password/{token}', name: 'security_resetPw')]
    public function resetPw($token, Request $request, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // On vérifie si on a ce token dans la base
        $user = $userRepository->findOneBy([
            'resetToken' => $token
        ]);
        // On vérifie si le createdTokenAt = now - 3h
        $now = new DateTimeImmutable();
        if (!$user) {
            $this->addFlash('danger', 'Jeton invalide ou expiré.');
            return $this->redirectToRoute('security_forgotPw');
        }
        
        if ($now > $user->getCreatedTokenAt()->modify('+ 3 hour')) {
            $this->addFlash('warning', 'Votre demande de mot de passe a expiré. Merci de la renouveler.');
            return $this->redirectToRoute('security_forgotPw');
        }
        
        // On vérifie si l'utilisateur existe
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Validation CSRF
            if (!$this->isCsrfTokenValid('reset_password', $request->request->get('_token'))) {
                $this->addFlash('danger', 'Jeton CSRF invalide');
                return $this->redirectToRoute('security_forgotPw');
            }
        
            // Logique de réinitialisation du mot de passe
            $user->setResetToken(null);
            $user->setCreatedTokenAt(null);
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $em->flush();
            $this->addFlash('success', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'passForm' => $form->createView()
        ]);
        // Si le token est invalide on redirige vers le login
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('security_login');
    }

        // Exemple d'action qui pourrait purifier un message avant de le transmettre
        public function someAction(Request $request)
        {
            // Création d'un message avec du HTML (potentiellement dangereux)
            $message = "<script>alert('XSS');</script><p>Bienvenue sur notre site, <a href='#'>cliquez ici</a></p>";
    
            // On instancie HTMLPurifier pour purifier le message
            $purifier = new HTMLPurifier(HTMLPurifier_Config::createDefault());
            $cleanMessage = $purifier->purify($message);
    
            // Ajouter le message purifié aux flash messages
            $this->addFlash('success', $cleanMessage);
    
            // Rendre la vue
            return $this->render('some_template.html.twig');
        }
}
