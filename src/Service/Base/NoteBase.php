<?php

namespace Note\Service\Base;

use Config\NoteConstants;
use Note\DTO\NoteDTO;

abstract class NoteBase
{
    protected $ticketAvailYears = [];  // years to filter tickets


    protected function getLastDayAtAfternoon()
    {
        // logic
        // 1. today is Tue, the date will be last Thu afternoon 13pm
        // 2. today is Thu, the date will be last Tue afternoon 13pm

        // Get today's date and day of the week
        $lastFoundDate = new \DateTime();
        $dayOfWeek = $lastFoundDate->format('D');

        // Set the default time to 13:59 ( PM)
        $time = "13:59";
        $dayOfWeekMapper = array(
            "Mon" => "-3 days",
            "Tue" => "-4 days",
            "Wed" => "-1 days",
            "Thu" => "-2 days",
            "Fri" => "-1 days",
            "Sat" => "-2 days",
            "Sun" => "-3 days"
        );

        $previousDay = $dayOfWeekMapper[$dayOfWeek] ?? "-1 days";
        $lastFoundDate->modify($previousDay)->setTime(14, 0);

        // enable when troubleshooting only
        //        if ($dayOfWeek == "Wed" or $dayOfWeek == "Thur") {
        //            error_log("Today is `$dayOfWeek`, the last Tue date is `" . $lastFoundDate->format('Y-m-d H:i') . "`");
        //        } else {
        //            error_log("Today is `$dayOfWeek`, the last Thur date is `" . $lastFoundDate->format('Y-m-d H:i') . "`");
        //        }

        return strtotime($lastFoundDate->format('Y-m-d H:i'));
    }

    protected function getTicketLatestStatus($tickets)
    {
        $latestStatus = [];
        // Sort each ticket by date DESC and get the latest status

        foreach ($tickets as $ticket => &$notes) {

            // what's in $notes variable?
            // {
            //  "note_count": 7,
            //  "notes": [ {..}, {..}, ... ]
            // }

            $_notes = $notes["notes"];

            // Filter notes with a non-empty status
            $validNotes = array_filter($_notes, function ($note) {
                return !empty(trim($note['status']));
            });

            usort($validNotes, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            // First one is the latest
            $latestStatus[$ticket] = !empty($validNotes) ? $validNotes[0]["status"] : NoteConstants::JIRA_STATUS_OPEN;
        }

        return $latestStatus;
    }

    protected function getAvailableTicketYears()
    {
        $uniqueYears = array_unique($this->ticketAvailYears);
        rsort($uniqueYears);
        return array_values($uniqueYears);
    }

    /**
     * Sort notes by date (DESC) inside each ticket group.
     *
     * @param array<string, NoteDTO[]> $notes
     * @return array<string, NoteDTO[]>
     */
    protected function sortNotesByDateDesc(array $notes): array
    {
        foreach ($notes as $ticket => &$noteList) {
            usort($noteList, function (NoteDTO $a, NoteDTO $b) {
                return strtotime($b->date) <=> strtotime($a->date);
            });
        }
        unset($noteList);

        return $notes;
    }
}
