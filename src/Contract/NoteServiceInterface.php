<?php

namespace Note\Contract;

interface NoteServiceInterface
{
    public function execute(?NoteRequestStrategyInterface $data = null);
    public function getRowId();
}
