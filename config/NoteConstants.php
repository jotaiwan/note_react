<?php

namespace Config;

use Symfony\Component\HttpFoundation\RequestStack;

class NoteConstants
{
    const JIRA_STATUS_OPEN = "Open";
    const NOTE_ONLY = "NOTE_ONLY";
    const MEETING = "MEETING"; // special ticket for meeting notes
    const DEFAULT_DAYS = 90;
    const NOTE_SITE = "\$SITE";

    public static function getNoteFile()
    {
        if (file_exists('/var/www/note_react/data/note.txt')) {
            // use this default file in docker container
            return '/var/www/note_react/data/note.txt';
        }
        $config = getenv('NOTE_DATA_FILE');
        // exit(self::replaceSiteToken($config));
        return self::replaceSiteToken($config);
    }

    public static function getCredentialJson()
    {
        if (file_exists('/etc/secrets/credential.json')) {
            // use this default file in docker container
            return '/etc/secrets/credential.json';
        }
        $config = getenv('NOTE_CREDENTIAL_JSON');
        return self::replaceSiteToken($config);
    }

    private static function replaceSiteToken($value)
    {
        $filePath = $value;
        if (strpos($value, self::NOTE_SITE) !== false) {
            $site = getenv('SITE') ?: 'note';
            $filePath = str_replace(self::NOTE_SITE, $site, $value);
        }

        if (!file_exists($filePath) && (strpos($value, self::NOTE_SITE) !== false)) {
            // to default note
            $filePath = str_replace(self::NOTE_SITE, "note", $value);
        }
        return $filePath;
    }
}
