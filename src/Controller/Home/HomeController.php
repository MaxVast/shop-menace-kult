<?php

namespace App\Controller\Home;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
#[Route('/', name: 'home', methods: ['GET'])]
class HomeController {
    public function __invoke(Environment $twig) : Response
    {
        return new Response($twig->render('home/home.html.twig', []), Response::HTTP_OK);
    }
}
