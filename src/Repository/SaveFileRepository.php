<?php

namespace  NoteReact\Repository;

use NoteReact\Contract\ReadFileRepositoryInterface;
use NoteReact\Contract\SaveFileRepositoryInterface;
use Config\NoteConstants;
use NoteReact\DTO\NoteDTO;

use Psr\Log\LoggerInterface;
use NoteReact\Util\LoggerTrait;
use NoteReact\Util\DateTimeUtil;

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

        // need to return json
        header('Content-Type: application/json; charset=utf-8');

        $note = urldecode($note);

        // write ticket to the txt file with date
        $inserted = $this->writeTicketToTextFile($ticket, $note, $status);
        $this->info("Ticket note saved: " . json_encode($inserted));

        if ($inserted) {
            $this->info("The inserted flag appears to be true, validating file size...");
            $fileSize = filesize($this->filePath);
            $backupFileSize = filesize($backupFile);
            if ($fileSize < $backupFileSize) {
                $this->info("[ERROR] New file `$fileSize` is smaller than backup file ($backupFileSize)!. " .
                    "something went wrong, rollback now!");
                $this->rollbackWorkListFromBackup($backupFile);
                $this->info("[ERROR] Failed to save new ticket note, rollback to previous state.");
                return array("success" => false, "savedTicket" => $ticket, "reason" => "rollback");
            } else {
                $this->info("New ticket `$ticket` is saved");
                unlink($backupFile);
                return array("success" => true, "savedTicket" => $ticket, "reason" => "");
            }
        }
        $this->error("The ticket is not saved: " . $ticket);
        return array("success" => false, "savedTicket" => null, "reason" => "failed");
    }

    public function writeTicketToTextFile($ticket, $note = "", $status = "")
    {
        $this->info("Saving the new note request: `$ticket`");

        if (strtoupper($ticket) == NoteConstants::NOTE_ONLY) {
            $status = "NoteOnly";
        } elseif (strtoupper($ticket) == NoteConstants::MEETING) {
            $status = "Meeting";
        } elseif (empty($status)) {
            $status = "Open";
        }

        $newRow = [
            strtoupper($ticket),
            DateTimeUtil::getCurrentDateTimeInTimezone(),
            $status,
            $note
        ];

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);

        $fp = fopen($this->filePath, 'w');
        if ($fp === false) {
            $this->error("Failed to open file for writing");
            return false;
        }

        if (isset($lines[0])) {
            fwrite($fp, $lines[0] . "\n");
        }
        fputcsv($fp, $newRow);

        $remaining = array_slice($lines, 1);
        foreach ($remaining as $line) {
            fwrite($fp, $line . "\n");
        }

        fclose($fp);

        return true;
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
        $filename = pathinfo($this->filePath, PATHINFO_FILENAME);

        // Get the extension
        $extension = pathinfo($this->filePath, PATHINFO_EXTENSION);

        // Get current date
        $date = date('Ymd');

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
