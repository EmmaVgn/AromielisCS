<?php

namespace App\Controller\Admin;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\OrderRepository;
use App\Repository\VisitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatsController extends AbstractController
{
    // Route principale pour afficher les stats avec les filtres
    #[Route('/admin/stats/{filter?month}', name: 'admin_stats')]
    public function stats(
        Request $request,
        VisitRepository $visitRepository,
        OrderRepository $orderRepository,
        string $filter = 'month'
    ): Response {
        // Récupération des filtres de date depuis la requête
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
    
        // Conversion des dates en objets DateTime
        $startDate = $startDate ? new \DateTime($startDate) : new \DateTime('-30 days');
        $endDate = $endDate ? new \DateTime($endDate) : new \DateTime();
    
        // Récupération des données filtrées pour les visites, commandes et revenu
        $visitStats = $visitRepository->countBySource($startDate, $endDate);
        $orderStats = $orderRepository->countOrdersByMonth(); // Il faut appliquer aussi les filtres sur les commandes
        $revenueStats = $orderRepository->countRevenueByMonth(); // Pareil ici
    
        // Performances web simulées
        $performanceStats = [
            'avgLoadTime' => mt_rand(1, 5) + mt_rand(0, 99) / 100,
            'apiResponseTime' => mt_rand(100, 500) . ' ms',
            'errorRate' => mt_rand(0, 5) . '%'
        ];
    
        return $this->render('admin/stats.html.twig', [
            'visitStats' => $visitStats,
            'orderStats' => $orderStats,
            'revenueStats' => $revenueStats,
            'performanceStats' => $performanceStats,
            'currentFilter' => $filter,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);
    }
    

    // Route pour l'exportation en CSV
    #[Route('/admin/stats/export', name: 'admin_stats_export')]
    public function exportStats(VisitRepository $visitRepository, OrderRepository $orderRepository): Response
    {
        // Récupérer les données des visites et des commandes
        $startDate = new \DateTime('-30 days');
        $endDate = new \DateTime();
        $visits = $visitRepository->countBySource($startDate, $endDate);
        $orders = $orderRepository->countRevenueByMonth();

        // Générer le contenu du fichier CSV
        $csvData = "Source,Visites,Commandes,Chiffre d'affaires (€)\n";
        foreach ($orders as $order) {
            $source = $order['month'] ?? 'Direct';
            $visitCount = 0;

            foreach ($visits as $visit) {
                if ($visit['source'] === $source) {
                    $visitCount = $visit['visitCount'];
                    break;
                }
            }

            $csvData .= "{$source},{$visitCount},{$order['orderCount']},{$order['totalRevenue']}\n";
        }

        // Retourner la réponse CSV
        $response = new Response($csvData);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="stats.csv"');

        return $response;
    }

    // Route pour l'exportation en PDF (optionnel)
    #[Route('/admin/stats/export/pdf', name: 'admin_stats_export_pdf')]
    public function exportPdf(VisitRepository $visitRepository, OrderRepository $orderRepository): Response
    {
        $startDate = new \DateTime('-30 days');
        $endDate = new \DateTime();

        $visits = $visitRepository->countBySource($startDate, $endDate);
        $orders = $orderRepository->getRevenueBySource($startDate, $endDate);
        $conversionRate = $visitRepository->getConversionRate($startDate, $endDate);
        $avgLoadTime = $visitRepository->getAverageLoadTime($startDate, $endDate);

        // Générer le contenu HTML pour le PDF
        $html = $this->renderView('admin/stats_pdf.html.twig', [
            'visits' => $visits,
            'orders' => $orders,
            'conversionRate' => $conversionRate,
            'avgLoadTime' => $avgLoadTime,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d')
        ]);

        // Générer le PDF
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourner la réponse PDF
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="stats.pdf"',
            ]
        );
    }
}
