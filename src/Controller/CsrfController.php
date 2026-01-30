<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfController extends AbstractController
{
    #[Route('/csrf/task', name: 'csrf_task', methods: ['GET'])]
    public function task(CsrfTokenManagerInterface $csrf): JsonResponse
    {
        return new JsonResponse([
            'token' => $csrf->getToken('task')->getValue(),
        ]);
    }
}