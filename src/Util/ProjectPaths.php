<?php

namespace  NoteReact\Util;

class ProjectPaths
{
    private static ?string $projectDir = null;

    // Prevent instantiation
    private function __construct() {}

    /**
     * Get the root project directory
     * @return string
     * @throws \Exception
     */
    public static function projectDir(string $startDir = __DIR__): string
    {
        if (self::$projectDir !== null) {
            return self::$projectDir;
        }

        $dir = $startDir;

        while (true) {
            // Assume the project root contains 'src' folder
            if (is_dir($dir . '/src')) {
                self::$projectDir = $dir;
                return $dir;
            }

            $parentDir = dirname($dir);

            if ($parentDir === $dir) {
                throw new \Exception('Project root directory not found!');
            }

            $dir = $parentDir;
        }
    }

    /**
     * Get the 'data' folder path, creating it if missing
     */
    public static function dataDir(): string
    {
        $path = self::projectDir() . '/data';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }
}
