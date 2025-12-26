<?php

namespace Note\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Example controller only
class HelloController extends AbstractController
{
    #[Route('/hello', name: 'app_hello')]
    public function index(): Response
    {
        // Render the template and pass the environment variable
        return $this->render('hello/index.html.twig', [
            'controller_name' => 'HelloController'
        ]);
    }
}
