<?php

namespace App\Controller\Admin;

use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/my-dashboard.html.twig', [
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LMM Painting – Back Office');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Back-office');

        yield MenuItem::linkToCrud('Produits', 'fa fa-cube', Product::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tags', Category::class);

        yield MenuItem::section('Website');
        yield MenuItem::linkToRoute('homepage', 'fa fa-home', 'home');
        yield MenuItem::linkToRoute('Produits', 'fa fa-list', 'product_index');
    }
}
