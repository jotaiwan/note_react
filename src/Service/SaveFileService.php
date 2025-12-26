<?php

// src/Service/NoteService.php
namespace Note\Service;

use Psr\Log\LoggerInterface;
use Note\Util\LoggerTrait;

use Note\Contract\NoteServiceInterface;
use Note\Contract\ReadFileRepositoryInterface;
use Note\Contract\SaveFileRepositoryInterface;
use Note\Contract\NoteRequestStrategyInterface;
use Note\Repository\ReadFileRepository;

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

        $isSaved = $this->saveFileRepository->saveNoteData($data);

        return [];
    }

    public function getRowId(?NoteRequestStrategyInterface $data = null)
    {
        if ($data !== null) {
            return $data->getRowId();
        }

        return null;
    }
}
