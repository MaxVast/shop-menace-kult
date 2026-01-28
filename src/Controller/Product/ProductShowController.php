<?php

namespace App\Controller\Product;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/boutique/produit/{slug}', name: 'product_show', methods: ['GET'])]
class ProductShowController {
    public function __invoke(Environment $twig, ProductRepository $productRepository, $slug) : Response
    {
        return new Response($twig->render('product/show.html.twig', [
            'product' => $productRepository->findOneBy(['slug'=>$slug]),
        ]), Response::HTTP_OK);
    }
}
