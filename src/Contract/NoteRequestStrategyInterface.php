<?php

namespace  NoteReact\Contract;

interface NoteRequestStrategyInterface
{
    public function execute(): array;
    public function getRowId();
}
