<?php

namespace  NoteReact\Repository;

use NoteReact\Contract\ReadFileRepositoryInterface;
use NoteReact\Contract\UpdateFileRepositoryInterface;
use Config\NoteConstants;
use NoteReact\DTO\NoteDTO;

use Psr\Log\LoggerInterface;
use NoteReact\Util\LoggerTrait;

class UpdateFileRepository implements UpdateFileRepositoryInterface
{
    use LoggerTrait;

    private ReadFileRepositoryInterface $readFileRepository;

    private string $filePath;

    public function __construct(
        ReadFileRepositoryInterface $readFileRepository,
        LoggerInterface $logger
    ) {
        $this->readFileRepository = $readFileRepository;
        $this->filePath = NoteConstants::getNoteFile();
        $this->setLogger($logger);
    }

    public function updateNoteData(int $rowId, string $newNote)
    {
        $allNotes = $this->readFileRepository->getAllNotes();

        foreach ($allNotes as $note) {
            if ($note->getRowId() === $rowId) {
                $note->setNote($this->readFileRepository->escapeNoteText($newNote));
                break;
            }
        }

        if ($this->saveToFile($allNotes)) {
            $this->info("The new note is updated in `$this->filePath`");
            return true;
        } else {
            $this->error("The new updated note didn't save into the file `$this->filePath`");
            // throw?? or null??
            return false;
        }
    }

    private function saveToFile($allNotes)
    {
        $beforeSaveFileSize = 0;
        if (file_exists($this->filePath)) {
            $beforeSaveFileSize = filesize($this->filePath);
        } else {
            throw new \Exception("File '{$this->filePath}' does not exist.");
        }

        $csvLines = [];

        // add header
        $csvLines[] = '"ticket","date","status","note"';

        // add data rows
        foreach ($allNotes as $note) {
            $csvLines[] = sprintf(
                '"%s","%s","%s","%s"',
                $note->getTicket(),
                $note->getDate(),
                $note->getStatus(),
                $note->getNote()
            );
        }


        // join lines into a single string
        $csvContent = implode("\n", $csvLines);

        // save to file
        file_put_contents($this->filePath, $csvContent);

        $afterSaveFileSize = filesize($this->filePath);

        return $afterSaveFileSize > $beforeSaveFileSize;
    }
}
