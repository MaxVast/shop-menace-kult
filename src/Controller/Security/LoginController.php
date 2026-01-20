<?php

namespace App\Controller\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

#[AsController]
#[Route('/login', name: 'login', methods: ['GET', 'POST'])]
class LoginController {
    public function __invoke(AuthenticationUtils $authenticationUtils, Environment $twig) : Response
    {
        return new Response($twig->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]), Response::HTTP_OK);
    }
}
