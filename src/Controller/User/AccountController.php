<?php

namespace App\Controller\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

#[AsController]
#[Route('/account', name: 'account_index', methods: ['GET'])]
#[IsGranted('ROLE_USER')]
class AccountController {
    public function __invoke(Environment $twig): Response
    {
        $orders =  [];

        return new Response($twig->render('user/account/index.html.twig', [
            'orders' => $orders,
        ]), Response::HTTP_OK);
    }
}
