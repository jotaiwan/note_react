<?php

namespace  NoteReact\Entity;

/**
 * ② Entity → The final form of the data (e.g., DB/table)
 * Purpose: Represents the structure of the data in the "database/file"

 * Features:
 * Looks the same as the data source
 * Usually has getters/setters
 * Data has already been organized by the Model

 * 📌 An Entity is a "normalized data object" (the model itself).
 */
class Note
{
    private string $ticket;
    private string $date;
    private string $status;
    private string $note;
    private int $rowId;

    public function __construct(string $ticket, string $date, string $status, string $note, int $rowId)
    {
        $this->ticket = $ticket;
        $this->date = $date;
        $this->status = $status;
        $this->note = $note;
        $this->rowId = $rowId;
    }

    // Getter / Setter
    public function getTicket(): string
    {
        return $this->ticket;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getNote(): string
    {
        return $this->note;
    }
    public function getRowId(): int
    {
        return $this->rowId;
    }

    public function setTicket(string $ticket): void
    {
        $this->ticket = $ticket;
    }
    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function setNote(string $note): void
    {
        $this->note = $note;
    }
    public function setRowId(int $rowId): void
    {
        $this->rowId = $rowId;
    }

    public function jsonSerialize(): array
    {
        return [
            'ticket' => $this->getTicket(),
            'date' => $this->getDate(),
            'status' => $this->getStatus(),
            'note' => $this->getNote(),
            'rowId' => $this->getRowId(),
        ];
    }
}
