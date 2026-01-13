<?php

namespace  NoteReact\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use NoteReact\Service\HtmlHeadService;
use NoteReact\Service\MenuService;
// use NoteReact\Service\NoteService;
use NoteReact\Service\NoteTableBuilder;
use NoteReact\Api\NoteApi;
use NoteReact\Factory\NoteFactory;
use Config\NoteConstants;

use NoteReact\CredentialReader\CredentialReader;

use Psr\Log\LoggerInterface;
use NoteReact\Util\LoggerTrait;

class NoteController extends AbstractController
{
    use LoggerTrait;

    private MenuService $menuService;
    private HtmlHeadService $htmlHeadService;
    private NoteFactory $noteFactory;

    // inject Service to Controller
    public function __construct(
        NoteFactory $noteFactory,
        HtmlHeadService $htmlHeadService,
        MenuService $menuService,
        LoggerInterface $logger
    ) {
        $this->htmlHeadService = $htmlHeadService;
        $this->menuService = $menuService;
        $this->noteFactory = $noteFactory;
        $this->setLogger($logger);
    }

    #[Route('/note/read/{days?}', name: 'note_read_last_{days}_days')]
    public function readNoteFromFile(?int $days): Response
    {
        $this->info("Rendering note read page.");
        $noteService = $this->noteFactory->createNoteService('read');

        $days = $days ?? NoteConstants::DEFAULT_DAYS;

        $noteRequest = $this->noteFactory->createRequestStrategy('read', array("days" => $days));
        $data = $noteService->execute($noteRequest);

        return $this->render('note/index.html.twig', $data);
    }
}
