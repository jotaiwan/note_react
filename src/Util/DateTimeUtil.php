<?php

namespace Note\Util;

/**
 * DateTimeUtil Class - A utility class for DateTime operations with timezone support
 */
class DateTimeUtil
{
    const ETC_GMT_PLUS_7 = 'Etc/GMT+7'; // UTC-7
    const AUS_SYDNEY = 'Australia/Sydney';

    /**
     * Get current date and time in a specified timezone
     * 
     * @param string $timezone The timezone, e.g. 'Asia/Taipei'
     * @return string The current date and time in Y-m-d H:i:s format
     */
    public static function getCurrentDateTimeInTimezone($timezone = self::ETC_GMT_PLUS_7)
    {
        $date = new \DateTime('now', new \DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Convert a date from one timezone to another
     *
     * @param string $dateTime The date and time to be converted, e.g. '2025-12-15 12:00:00'
     * @param string $fromTimezone The source timezone, e.g. 'Asia/Taipei'
     * @param string $toTimezone The target timezone, e.g. 'America/New_York'
     * @return string The converted date and time in Y-m-d H:i:s format
     */
    public static function convertTimezone($dateTime, $fromTimezone, $toTimezone)
    {
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTimezone));
        $date->setTimezone(new \DateTimeZone($toTimezone));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Add days to a given date
     *
     * @param string $date The starting date, e.g. '2025-12-15'
     * @param int $days The number of days to add, e.g. 10
     * @return string The new date after adding the days
     */
    public static function addDays($date, $days)
    {
        $dateObj = new \DateTime($date);
        $dateObj->modify("+$days days");
        return $dateObj->format('Y-m-d');
    }

    /**
     * Subtract days from a given date
     *
     * @param string $date The starting date, e.g. '2025-12-15'
     * @param int $days The number of days to subtract, e.g. 5
     * @return string The new date after subtracting the days
     */
    public static function subtractDays($date, $days)
    {
        $dateObj = new \DateTime($date);
        $dateObj->modify("-$days days");
        return $dateObj->format('Y-m-d');
    }

    /**
     * Get the difference between two dates
     *
     * @param string $date1 The first date, e.g. '2025-12-15'
     * @param string $date2 The second date, e.g. '2025-12-10'
     * @return string The difference in days
     */
    public static function getDateDifference($date1, $date2)
    {
        $dateObj1 = new \DateTime($date1);
        $dateObj2 = new \DateTime($date2);
        $interval = $dateObj1->diff($dateObj2);
        return $interval->days . ' days';
    }

    /**
     * Get the start of the day for a given date
     *
     * @param string $date The date, e.g. '2025-12-15'
     * @return string The date with the time set to 00:00:00
     */
    public static function getStartOfDay($date)
    {
        $dateObj = new \DateTime($date);
        return $dateObj->setTime(0, 0, 0)->format('Y-m-d H:i:s');
    }

    /**
     * Get the end of the day for a given date
     *
     * @param string $date The date, e.g. '2025-12-15'
     * @return string The date with the time set to 23:59:59
     */
    public static function getEndOfDay($date)
    {
        $dateObj = new \DateTime($date);
        return $dateObj->setTime(23, 59, 59)->format('Y-m-d H:i:s');
    }

    /**
     * Format a given date according to the specified format
     *
     * @param string $date The date, e.g. '2025-12-15 12:00:00'
     * @param string $format The desired format, e.g. 'd/m/Y H:i:s'
     * @return string The formatted date
     */
    public static function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        $dateObj = new \DateTime($date);
        return $dateObj->format($format);
    }

    /**
     * Get the current Unix timestamp for a specific timezone
     *
     * @param string $timezone The timezone, e.g. 'Asia/Taipei'
     * @return int The Unix timestamp
     */
    public static function getUnixTimestamp($timezone = self::ETC_GMT_PLUS_7)
    {
        $date = new \DateTime('now', new \DateTimeZone($timezone));
        return $date->getTimestamp();
    }

    /**
     * Check if a given date is a valid date
     *
     * @param string $date The date to be checked, e.g. '2025-12-15'
     * @return bool True if valid, false otherwise
     */
    public static function isValidDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
