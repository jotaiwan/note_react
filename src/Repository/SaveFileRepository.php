<?php

namespace Note\Repository;

use Note\Contract\ReadFileRepositoryInterface;
use Note\Contract\SaveFileRepositoryInterface;
use Config\NoteConstants;
use Note\DTO\NoteDTO;

use Psr\Log\LoggerInterface;
use Note\Util\LoggerTrait;
use Note\Util\DateTimeUtil;

class SaveFileRepository implements SaveFileRepositoryInterface
{
    use LoggerTrait;

    private $filePath;
    private ReadFileRepositoryInterface $readFileRepository;

    public function __construct(
        ReadFileRepositoryInterface $readFileRepository,
        LoggerInterface $logger
    ) {
        $this->readFileRepository = $readFileRepository;
        $this->filePath = NoteConstants::getNoteFile();
        $this->setLogger($logger);
    }

    /**
     * Note only: $data array should be {"ticket":"NOTE_ONLY","note":{note_text}","status":""}
     */
    public function saveNoteData($data)
    {
        // backup the file first in case something went wrong and allow to rollback
        $backupFile = $this->copyWorkListToFolder();

        $request = $data->execute();

        $ticket = $request['ticket'];
        $note = $request['note'];
        $status = $request['status'];

        if (preg_match('/^\d+$/', $ticket)) {
            ## if ticket is digit number only, add "APPSUP-" to the front
            ## eg ticket is 7723, it will be APPSUP-7723
            $ticket = "APPSUP-" . $ticket;
        }

        // need to return json
        header('Content-Type: application/json; charset=utf-8');

        $note = urldecode($note);

        // write ticket to the txt file with date
        $inserted = $this->writeTicketToTextFile($ticket, $note, $status);
        $this->info("Ticket note saved: " . json_encode($inserted));

        if ($inserted) {
            $fileSize = filesize($this->filePath);
            $backupFileSize = filesize($backupFile);
            if ($fileSize < $backupFileSize) {
                $this->info("[ERROR] New file `$fileSize` is smaller than backup file ($backupFileSize)!. " .
                    "something went wrong, rollback now!");
                $this->rollbackWorkListFromBackup($backupFile);
                $this->info("[ERROR] Failed to save new ticket note, rollback to previous state.");
            } else {
                $this->info("New ticket `$ticket` is saved");
                unlink($backupFile);
                return array("savedTicket" => $ticket);
            }
        }
        return array("savedTicket" => "");
    }

    public function writeTicketToTextFile($ticket, $note = "", $status = "")
    {
        $this->info("Saving the new note request: $ticket");

        if (strtoupper($ticket) == NoteConstants::NOTE_ONLY) {
            $status = "NoteOnly";          // no matter what, it will be note_only
        } elseif (strtoupper($ticket) == NoteConstants::MEETING) {
            $status = "Meeting";
        } elseif (empty($status)) {
            $allNotes = $this->readFileRepository->getAllNotes($ticket);
            if (in_array($ticket, array_keys($allNotes))) {
                $notes = $allNotes[$ticket];
                usort($notes, function ($a, $b) {
                    return strtotime($b->getDate()) - strtotime($a->getDate());
                });

                $statuses = array_map(function ($note) {
                    return $note->getStatus();
                }, $notes);

                $status = !empty($statuses) ? $statuses[0] : "Open";
            } else {
                $status = "Open";
            }

            $this->info("The request to save the note with ticket '$ticket' is in status: '$status'.");
        }

        $this->info("Preparing to write ticket: `$ticket`...");

        // Wrap note in quotes
        $newLine = strtoupper($ticket) . "," . DateTimeUtil::getCurrentDateTimeInTimezone() . "," . '"' . $status . '"' . "," .
            '"' . $note . '"';

        // Step 1: Read file lines into an array
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);

        // Step 2: Insert new line after first line (index 0)
        array_splice($lines, 1, 0, $newLine);

        $this->info("Saving the line to file `" . $this->filePath . "`: " . json_encode($newLine));
        $this->info('file_exists=' . (file_exists($this->filePath) ? 'yes' : 'no'));
        $this->info('is_writable=' . (is_writable(dirname($this->filePath)) ? 'yes' : 'no'));

        // Step 3: Write updated lines back to the file
        $result = file_put_contents($this->filePath, implode("\n", $lines));

        // Step 4: validate the insert
        return $this->validateInsert($newLine);
    }

    public function validateInsert($new)
    {
        $content = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->info("Does insert success: " . (strpos($content[1], $new) !== false) ? "saved." : "not saved.");
        return strpos($content[1], $new) !== false;
    }

    private function copyWorkListToFolder()
    {
        $backupFullPathFile = $this->getBackupFullPathFile();
        $this->info("Setup backup file with full path: $backupFullPathFile");
        if (!copy($this->filePath, $backupFullPathFile)) {
            $this->info("[ERROR] failed to copy from `$this->filePath` to `$backupFullPathFile`!!");
            throw new \RuntimeException("Failed to copy file to $backupFullPathFile");
        }
        return $backupFullPathFile;
    }

    public function rollbackWorkListFromBackup($backupFile)
    {
        if (!file_exists($backupFile)) {
            error_log("[ERROR] Backup file `$backupFile` does not exist, cannot rollback!");
            throw new \RuntimeException("Backup file `$backupFile` does not exist, cannot rollback!");
        }
        if (!copy($backupFile, $this->filePath)) {
            error_log("[ERROR] Failed to rollback from `$backupFile` to `$this->filePath`!!");
            throw new \RuntimeException("Failed to rollback from `$backupFile` to `$this->filePath`!!");
        }
        error_log("Rollback from `$backupFile` to `$this->filePath` is successful.");
    }

    public function getBackupFullPathFile()
    {
        // Get the filename without extension
        $this->info("111");
        $filename = pathinfo($this->filePath, PATHINFO_FILENAME);

        $this->info("222");
        // Get the extension
        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);

        $this->info("333");
        // Get current date
        $date = date('Ymd');

        $this->info("444");
        $backupFolder = dirname($this->filePath) . '/backup';
        $this->info(">> $backupFolder");
        if (!is_dir($backupFolder)) {
            $this->info("555");
            if (mkdir($backupFolder, 0777, true)) {  // 0777 = permissions, true = recursive
                $this->info("Created backup folder `$backupFolder` successfully!");
            } else {
                $this->info("Failed to create backup folder `$backupFolder`!!!");
            }
        }

        // Build the destination path
        return $backupFolder . '/' . $filename . '_' . $date . '.' . $extension;
    }
}
