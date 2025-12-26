<?php

namespace Note\Contract;

interface NoteRequestStrategyInterface
{
    public function execute(): array;
    public function getRowId();
}
