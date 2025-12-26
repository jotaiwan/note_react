<?php

namespace Note;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        // 1️⃣ set timezone to Etc/GMT+7 by default
        date_default_timezone_set('Etc/GMT+7');

        // 2️⃣ We are importing the configuration from ~/.bashrc and put as environment
        $home = trim(shell_exec('getent passwd ' . get_current_user() . ' | cut -d: -f6'));
        putenv("HOME=$home");

        // import ~/.bashrc's export variables
        if ($home && file_exists($home . '/.bashrc')) {
            $lines = file($home . '/.bashrc', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^export (\w+)=(.*)$/', $line, $matches)) {
                    $key = $matches[1];
                    $value = $matches[2];

                    // Simple handling: only check for BASE_NAME manually
                    if ($key === 'HOME_AND_BASE') {
                        $baseName = $_ENV['BASE_NAME'] ?? getenv('BASE_NAME') ?? '';
                        $home = $_ENV['HOME'] ?? getenv('HOME') ?? '';
                        $value = $home . ($baseName !== '' ? "/$baseName" : '');
                    }

                    // If other variables contain ${VAR}, just replace them simply
                    $value = preg_replace_callback('/\$\{(\w+)\}/', function ($m) {
                        return $_ENV[$m[1]] ?? getenv($m[1]) ?? '';
                    }, $value);

                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }

        parent::__construct($environment, $debug);
    }
}
