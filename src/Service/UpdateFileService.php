<?php

// src/Service/NoteService.php
namespace Note\Service;

use Note\Contract\NoteRequestStrategyInterface;

use Note\Contract\NoteServiceInterface;
use Note\Contract\ReadFileRepositoryInterface;
use Note\Contract\UpdateFileRepositoryInterface;
use Note\Service\NoteBuilderService;;

use Note\DTO\NoteDTO;
use Note\Util\LoggerTrait;

use Psr\Log\LoggerInterface;

class UpdateFileService implements NoteServiceInterface
{
    use LoggerTrait;

    private $updateFileRepository;
    private $readFileRepository;
    private $noteBuilderService;

    public function __construct(
        UpdateFileRepositoryInterface $updateFileRepository,
        ReadFileRepositoryInterface $readFileRepository,
        NoteBuilderService $noteBuilderService,
        LoggerInterface $logger
    ) {
        $this->updateFileRepository = $updateFileRepository;
        $this->readFileRepository = $readFileRepository;
        $this->noteBuilderService = $noteBuilderService;
        $this->setLogger($logger);
    }

    public function execute(?NoteRequestStrategyInterface $data = null): mixed
    {
        // Execute the request strategy and get the resulting array
        $request = $data->execute();

        // Retrieve the rowId and note from the returned array
        $rowId = $request['rowId'] ?? null;
        $note = $request['note'] ?? null;

        if ($rowId === null || $note === null) {
            throw new \Exception("Update row_id and data are required for update operation");
        }

        $this->info("Updating note's row_id `{$rowId}` with new note: " . $note);

        $isUpdated = $this->updateFileRepository->updateNoteData($rowId, $note);
        if ($isUpdated) {
            $note = $this->readFileRepository->getNoteById($rowId);
            $htmlNote = $this->noteBuilderService->createHtmlNote($note->note);
            $this->info("Successfully updated note's row_id {$rowId}");
        } else {
            $htmlNote = $this->noteBuilderService->createHtmlNote($note);
        }

        return [
            'rowId' => $rowId,
            'note' => $htmlNote
        ];
    }

    public function getRowId(?NoteRequestStrategyInterface $data = null)
    {
        if ($data !== null) {
            return $data->getRowId();
        }

        return null;
    }
}
