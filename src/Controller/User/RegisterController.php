<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Form\Type\User\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

#[AsController]
#[Route('/register', name: 'register', methods: ['GET', 'POST'])]
class RegisterController
{
    public function __invoke(Environment $twig, FormFactoryInterface $formFactory,
        UserPasswordHasherInterface $passwordHasher, Request $request,
        RouterInterface $router, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $formFactory->create(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                );

                $user->setPassword($hashedPassword);

                $user = $form->getData();
                $userRepository->persistAndSave($user);

                return new RedirectResponse($router->generate('home'));
            }
        } catch (LogicException $e) {
        }

        return new Response($twig->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]), Response::HTTP_OK);
    }
}
