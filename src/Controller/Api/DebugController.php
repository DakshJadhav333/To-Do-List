<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DebugController extends AbstractController
{
    #[Route('/api/debug/body', name: 'api_debug_body', methods: ['POST'])]
    public function body(Request $request): Response
    {
        return new Response(
            "Content-Type: ".$request->headers->get('content-type')."\n\n".
            "Raw Body:\n".$request->getContent()
        );
    }
}