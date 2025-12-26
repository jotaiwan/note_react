<?php

namespace Note\Repository;

use Note\Contract\ReadFileRepositoryInterface;
use Config\NoteConstants;
use Note\Entity\Note;

use Psr\Log\LoggerInterface;
use Note\Util\LoggerTrait;

class ReadFileRepository implements ReadFileRepositoryInterface
{
    use LoggerTrait;

    private $filePath;

    public function __construct(LoggerInterface $logger)
    {
        $this->filePath = NoteConstants::getNoteFile();
        $this->setLogger($logger);
    }

    public function getRawNotes(): array
    {
        $result = [];
        if (!file_exists($this->filePath)) {
            exit("11 : $this->filePath");
            return $result;
        }

        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            return $result;
        }

        // Skip the header row by calling fgetcsv once
        fgetcsv($handle, 0, ",", "\"", "\\");

        // Start processing from line 2 (the actual data)
        $rowNum = 2;
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($row) < 4) {
                continue;
            }

            /**
             * Note Only: Keep the following commented code for future reference.
             * When you want to output JSON, for example: echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
             * You must enable the two options below, otherwise your JSON string will look weird and won’t be formatted correctly!!!
             */
            // // decode embedded escape sequences like \n, \t, etc.
            // $row[3] = stripcslashes($row[3]);
            // $row[3] = "<pre>" . strip_tags($row[3], '<i>') . "</pre>";

            $result[] = ['row' => $row, 'rowNum' => $rowNum];
            $rowNum++;
        }

        fclose($handle);
        return $result;
    }

    /**
     * return example
     * {
     *   "APPSUP-7723": [ NoteDTO, NoteDTO, ... ],
     * }
     */
    public function getAllNotes(string $search = ""): array
    {
        $rawNotes = $this->getRawNotes();
        $result = [];

        foreach ($rawNotes as $item) {
            // Note Only: what's this for ? [$ticket, $date, $status, $note] = $item['row'];, it's for producing the following arra
            // $ticket = $row[0];
            // $date   = $row[1];
            // $status = $row[2];
            // $note   = $row[3];
            [$ticket, $date, $status, $note] = $item['row'];
            $rowNum = $item['rowNum'];

            // The repository returns an Entity (e.g., Entity/Note.php).
            // This represents the data structure as stored in the database or read from a file (the data source).
            // The Entity mirrors the original data format and is primarily used for persistence operations.
            $result[] = new Note(
                ticket: $ticket,
                date: $date,
                status: $status,
                note: $note,
                rowId: $rowNum
            );
        }
        return $result;
    }


    /**
     * getNoteById
     * @param int id: row id (should match to file row)
     * @return NoteDTO|null
     */
    public function getNoteById(int $rowId)
    {
        $rawNotes = $this->getRawNotes();

        foreach ($rawNotes as $item) {
            [$ticket, $date, $status, $note] = $item['row'];
            $rowNum = $item['rowNum'];

            if ($rowNum === $rowId) {
                $this->info("Found note's row_id {$rowId}: Ticket: {$ticket}, Date: {$date}, Status: {$status}, Note: {$note}");
                return new Note(
                    ticket: $ticket,
                    date: $date,
                    status: $status,
                    note: $note,
                    rowId: $rowNum
                );
            }
        }

        $this->info("Note cannnot find row_id {$rowId}");
        return null;
    }

    public function getLatestStatusPerTicket($ticketNotes)
    {
        $latestStatus = [];
        foreach ($ticketNotes as $ticket => $noteResults) {
            $latest = null;
            $notes = $noteResults['notes'] ?? [];
            foreach ($notes as $note) {
                if (!isset($note['date']) || !isset($note['status'])) {
                    error_log("Log if any missing fields for date or status in the ticket `$ticket` note: " .
                        json_encode($note));
                    continue;
                }
                if ($latest === null || strtotime($note['date']) > strtotime($latest['date'])) {
                    $latest = $note;
                }
            }
            if ($latest) {
                $latestStatus[$ticket] = $latest['status'];
            }
        }

        return $latestStatus;
    }

    public function escapeNoteText(string $note): string
    {
        // follow the sqence to ecape properly
        $note = str_replace('\\', '\\\\', $note);
        // Convert newlines to escape sequences
        $note = str_replace("\n", "\\n", $note);
        $note = str_replace('"', '\\"', $note);

        return $note;
    }
}
