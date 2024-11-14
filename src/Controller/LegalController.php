<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    #[Route('/all-mentions', name: 'legal_all')]
    public function all(): Response
    {
        return $this->render('legal/index.html.twig');
    }

    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/_legal.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'politique_confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('legal/_confidentiality.html.twig');
    }

    #[Route('/cgv', name: 'cgv')]
    public function cgv(): Response
    {
        return $this->render('legal/_cgv.html.twig');
    }

    #[Route('/politique-de-remboursement', name: 'politique_remboursement')]
    public function politiqueRemboursement(): Response
    {
        return $this->render('legal/_cancelled.twig');
    }

    #[Route('/politique-de-cookies', name: 'politique_cookies')]
    public function politiqueCookies(): Response
    {
        return $this->render('legal/_cookies.html.twig');
    }

    #[Route('/plan-du-site', name: 'plan_du_site')]
    public function planDuSite(): Response
    {
        return $this->render('legal/_map_site.html.twig');
    }


    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render('legal/_cgu.html.twig');
    }


}
