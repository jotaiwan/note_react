<?php

namespace Note\DTO;

class NoteDTO implements \JsonSerializable
{
    public string $ticket;
    public string $date;
    public string $status;
    public string $note;
    public string $rowId;

    public function __construct(string $ticket, string $date, string $status, string $note, int $rowId)
    {
        $this->ticket = $ticket;
        $this->date = $date;
        $this->status = $status;
        $this->note = $note;
        $this->rowId = $rowId;
    }

    public function jsonSerialize(): array
    {
        return [
            'ticket' => $this->ticket,
            'date' => $this->date,
            'status' => $this->status,
            'note' => $this->note,
            'rowId' => $this->rowId,
        ];
    }
}
