<?php

namespace Config;

use Symfony\Component\HttpFoundation\RequestStack;

class NoteConstants
{
    const HISTORY_DATE = "date";
    const HISTORY_TICKET = "ticket";
    const HISTORY_NOTE = "note";
    const HISTORY_DAYS = "days";
    const HISTORY_YEAR = "year";
    const HISTORY_STATUS = "status"; // status of the ticket, eg: "done", "in progress", "waiting for response"

    const JIRA_STATUS_OPEN = "Open";

    const NOTE_ONLY = "NOTE_ONLY";
    const MEETING = "MEETING"; // special ticket for meeting notes

    const READ_ACTION = "read";
    const SAVE_ACTION = "save";
    const BUILD_ACTION = "build";
    const SEARCH_ACTION = "search";
    const UPDATE_ACTION = "update"; // update existing ticket note

    const DEFAULT_DAYS = 90;

    const LOAD_TICKET = "load_ticket"; // load ticket details, eg: ticket title, status, etc.N

    public static function getNoteFile()
    {
        $config = getenv('NOTE_DATA_FILE');
        // exit(self::replaceSiteToken($config));
        return self::replaceSiteToken($config);
    }

    public static function getCredentialJson()
    {
        $config = getenv('NOTE_CREDENTIAL_JSON');
        return self::replaceSiteToken($config);
    }

    private static function replaceSiteToken($value)
    {
        $filePath = $value;
        if (strpos($value, '{SITE}') !== false) {
            $site = SITE;
            $filePath = str_replace('{SITE}', $site, $value);
        }

        if (!file_exists($filePath) && (strpos($value, '{SITE}') !== false)) {
            // to default note
            $filePath = str_replace('{SITE}', "note", $value);
        }
        return $filePath;
    }
}
