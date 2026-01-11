<?php

namespace  NoteReact\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Example controller only
class EnvController extends AbstractController
{
    #[Route('/env-test', name: 'env_test')]
    public function index(): Response
    {
        $noteOfficeDataFile = getenv('NOTE_OFFICE_DATA_FILE');

        if (!$noteOfficeDataFile) {
            return new Response('Empty');
        }

        return $this->render('env-test/index.html.twig', [
            'note_office_data' => $noteOfficeDataFile,
        ]);
    }
}
