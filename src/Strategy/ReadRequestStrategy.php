<?php
// src/Strategy/ReadRequestStrategy.php
namespace  NoteReact\Strategy;

use Config\NoteConstants;
use NoteReact\Contract\NoteRequestStrategyInterface;

class ReadRequestStrategy implements NoteRequestStrategyInterface
{
    private $request;

    private int $days;

    public function __construct(array $data)
    {
        $this->request = $data;
        // Prefer explicit 'days' parameter; fall back to 'rowId' if provided, otherwise use default
        $this->days = isset($this->request['days']) ? (int)$this->request['days'] : (
            isset($this->request['rowId']) && is_numeric($this->request['rowId']) ? (int)$this->request['rowId'] : NoteConstants::DEFAULT_DAYS
        );
    }

    public function execute(): array
    {
        return [
            'rowId' => $this->request['rowId'] ?? null,
            'days' => $this->days
        ];
    }

    public function getRowId(): ?int
    {
        return $this->request['rowId'] ?? null;  // Return rowId from request
    }

    public function getDays(): ?int
    {
        return $this->days ?? NoteConstants::DEFAULT_DAYS;
    }
}
