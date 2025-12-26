<?php

namespace Note\Strategy;

use Note\Contract\NoteRequestStrategyInterface;

class UpdateRequestStrategy implements NoteRequestStrategyInterface
{
    private $request;

    public function __construct(array $data)
    {
        $this->request = $data;
    }

    public function execute(): array
    {
        return [
            'rowId' => $this->request['rowId'],
            'note' => $this->request['note']
        ];
    }

    public function getRowId(): ?int
    {
        return $this->request['rowId'] ?? null;  // Return rowId from request
    }
}
