<?php
// src/Controller/ReadmeController.php

namespace Note\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;

class ReadmeController extends AbstractController
{
    /**
     * Display the README page.
     *
     * This route shows the README content to the user.
     *
     * @return Response
     */
    #[Route('/readme', name: 'readme')]
    public function showReadme(): Response
    {
        // define README.html file path
        $readmeFile = $this->getParameter('kernel.project_dir') . '/README.html';
        if (!file_exists($readmeFile)) {
            return new Response('README.html file not found.', Response::HTTP_NOT_FOUND);
        }

        $content = file_get_contents($readmeFile);
        return new Response($content);
    }
}
