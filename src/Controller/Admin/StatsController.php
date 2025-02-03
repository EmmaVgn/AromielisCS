<?php

namespace App\Controller\Admin;

use App\Repository\VisitRepository;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatsController extends AbstractController
{
    #[Route('/admin/stats/{filter}', name: 'admin_stats')]
    public function stats(
        VisitRepository $visitRepository,
        OrderRepository $orderRepository,
        string $filter = 'month'
    ): Response {

        // Récupérer les stats en fonction du filtre
        $visitStats = $visitRepository->countBySourceWithFilter($filter ?? 'month');
        $orderStats = $orderRepository->countOrdersByMonth();
        $revenueStats = $orderRepository->countRevenueByMonth();

        // Simuler des performances web
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
        ]);
    }

}
