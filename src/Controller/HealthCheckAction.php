<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/health-check', name: 'health_check', methods: ['GET'])]
final class HealthCheckAction
{
    public function __invoke(): Response
    {
        return new JsonResponse('OK');
    }
}