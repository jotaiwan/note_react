<?php

// src/Service/NoteService.php
namespace  NoteReact\Service;

use Psr\Log\LoggerInterface;
use NoteReact\Util\LoggerTrait;

use NoteReact\Contract\NoteServiceInterface;
use NoteReact\Contract\ReadFileRepositoryInterface;
use NoteReact\Contract\SaveFileRepositoryInterface;
use NoteReact\Contract\NoteRequestStrategyInterface;
use NoteReact\Repository\ReadFileRepository;

class SaveFileService implements NoteServiceInterface
{
    use LoggerTrait;

    private $readFileRepository;
    private $saveFileRepository;

    public function __construct(
        SaveFileRepositoryInterface $saveFileRepository,
        ReadFileRepositoryInterface $readFileRepository,
        LoggerInterface $logger
    ) {
        $this->saveFileRepository = $saveFileRepository;
        $this->readFileRepository = $readFileRepository;
        $this->setLogger($logger);
    }

    public function execute(?NoteRequestStrategyInterface $data = null)
    {
        if ($data === null) {
            throw new \Exception("New save data are required for update operation");
        }

        // should return array("savedTicket" => $ticket) or array("savedTicket" => '');
        return $this->saveFileRepository->saveNoteData($data);
    }

    public function getRowId(?NoteRequestStrategyInterface $data = null)
    {
        if ($data !== null) {
            return $data->getRowId();
        }

        return null;
    }
}
