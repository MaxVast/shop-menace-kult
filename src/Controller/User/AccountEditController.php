<?php

namespace App\Controller\User;

use App\Entity\User\User;
use App\Form\Type\User\UserProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

#[AsController]
#[Route('/account/edit', name: 'account_edit', methods: ['GET', 'POST'])]
#[IsGranted('ROLE_USER')]
class AccountEditController
{
    public function __invoke(Environment $twig, FormFactoryInterface $formFactory, Request $request, Security $security,
        UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, RouterInterface $router): Response
    {
        $user = $security->getUser();
        if (!$user instanceof User) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $form = $formFactory->create(UserProfileType::class, $user);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $form->get('password')->getData();
                if ($password) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $password
                    );
                    $user->setPassword($hashedPassword);
                }

                $user = $form->getData();
                $userRepository->persistAndSave($user);

                return new RedirectResponse($router->generate('account_index'));
            }
        } catch (LogicException $e) {
        }

        return new Response($twig->render('user/account/edit.html.twig', [
            'form' => $form->createView(),
        ]), Response::HTTP_OK);
    }
}
