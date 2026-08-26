<?php

namespace App\Controller\Security;

use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/logout', name: 'logout')]
class LogoutController
{
    public function __invoke()
    {
    }
}
