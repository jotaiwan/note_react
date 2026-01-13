<?php

// src/Service/NoteService.php
namespace  NoteReact\Service;

use Config\NoteConstants;
use NoteReact\Contract\ReadFileRepositoryInterface;
use NoteReact\Contract\NoteServiceInterface;
use NoteReact\Contract\NoteRequestStrategyInterface;
use NoteReact\Service\HtmlHeadService;
use NoteReact\Service\MenuService;
use NoteReact\Service\NoteBuilderService;
use NoteReact\Service\Base\NoteBase;
use NoteReact\Strategy\ReadRequestStrategy;

use NoteReact\DTO\NoteDTO;

use NoteReact\CredentialReader\CredentialReader;

use NoteReact\Util\LoggerTrait;

class ReadFileService extends NoteBase implements NoteServiceInterface
{
    use LoggerTrait;

    private ReadFileRepositoryInterface $readFileRepository;
    private HtmlHeadService $htmlHeadService;
    private MenuService $menuService;
    private NoteBuilderService $noteBuilderService;

    public function __construct(
        ReadFileRepositoryInterface $readFileRepository,
        HtmlHeadService $htmlHeadService,
        MenuService $menuService,
        NoteBuilderService $noteBuilderService
    ) {
        $this->readFileRepository = $readFileRepository;
        $this->htmlHeadService = $htmlHeadService;
        $this->menuService = $menuService;
        $this->noteBuilderService = $noteBuilderService;
    }

    public function execute(?NoteRequestStrategyInterface $data = null)
    {
        $rowId = $this->getRowId($data);
        if ($rowId) {
            $this->info("Reading note's row_id: {$rowId}");
            $note = $this->readFileRepository->getNoteById($rowId);
            return $this->createNoteDTO($note);
        }

        // read note and return array(\Entity\Note)
        $allNotes = $this->readFileRepository->getAllNotes();
        $this->info("Fetched total `" . count($allNotes) . "` notes");

        // get days from ReadRequestStrategy
        $days = ($data instanceof ReadRequestStrategy) ? $data->getDays() : NoteConstants::DEFAULT_DAYS;
        $this->info("Loading notes from the last `$days` days");

        // filter days
        $filteredNotes = array_filter($allNotes, function ($note) use ($days) {
            $noteDate = new \DateTime($note->getDate());
            $startDate = (new \DateTime())->modify("-$days days");
            return $noteDate >= $startDate;
        });

        $notes = $this->mapNotesToGroupedDTOsByTicket($filteredNotes);

        $notes = $this->sortNotesByDateDesc($notes);

        $this->info("Fetched " . count($filteredNotes) . " notes (grouped by ticket) from the last $days days.");

        $tableRows = $this->noteBuilderService->buildTableRows($notes);
        return $tableRows;
    }

    public function getRowId(?NoteRequestStrategyInterface $data = null)
    {
        return ($data !== null) ? $data->getRowId() : null;
    }

    private function mapNotesToGroupedDTOsByTicket(array $notes)
    {
        $map = [];
        foreach ($notes as $note) {
            $map[$note->getTicket()][] = $this->createNoteDTO($note);
        }
        return $map;
    }

    private function createNoteDTO($note)
    {
        return new NoteDTO(
            ticket: $note->getTicket(),
            date: $note->getDate(),
            status: $note->getStatus(),
            note: $note->getNote(),
            rowId: $note->getRowId()
        );
    }
}
