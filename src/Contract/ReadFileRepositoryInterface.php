<?php

namespace Note\Contract;

interface ReadFileRepositoryInterface
{
    public function getRawNotes();
    public function getAllNotes(string $search = "");
    public function getNoteById(int $rowId);
    public function escapeNoteText(string $note): string;
}
