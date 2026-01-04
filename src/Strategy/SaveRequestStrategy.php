<?php

namespace  NoteReact\Strategy;

use NoteReact\Contract\NoteRequestStrategyInterface;

class SaveRequestStrategy implements NoteRequestStrategyInterface
{
    private $request;

    public function __construct(array $data)
    {
        $this->request = $data;
    }

    public function execute(): array
    {
        ## if ticket is digit number only, add "APPSUP-" to the front
        ## eg ticket is 7723, it will be APPSUP-7723
        $ticket = empty($this->request['ticket'])
            ? "NOTE_ONLY"
            : (preg_match('/^\d+$/', $this->request['ticket'])
                ? "APPSUP-" . $this->request['ticket']
                : $this->request['ticket']);

        return [
            'ticket' => $ticket,
            'status' => $this->request['status'],
            'note' => $this->request['note'],
        ];
    }

    public function getRowId(): ?int
    {
        return $this->request['rowId'] ?? null;  // Return rowId from request
    }
}
