<?php

namespace Note\Contract;

interface UpdateFileRepositoryInterface
{
    public function updateNoteData(int $rowId, string $newNote);
}
