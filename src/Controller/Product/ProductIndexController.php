<?php

namespace App\Controller\Product;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/boutique', name: 'product_index', methods: ['GET'])]
class ProductIndexController {
    public function __invoke(Environment $twig, ProductRepository $productRepository) : Response
    {
        return new Response($twig->render('product/list.html.twig', [
            'products' => $productRepository->findAll(),
        ]), Response::HTTP_OK);
    }
}
