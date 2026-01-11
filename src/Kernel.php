<?php

namespace NoteReact;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

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

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle(),
            new MonologBundle(),
            new NelmioCorsBundle(),
        ];
    }


    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // Import routes from config and controllers using PHP 8 attributes
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../src/Controller/', 'attribute');
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        $container->import('../config/{services}.yaml');
        $container->import('../config/{services}_' . $this->environment . '.yaml', null);
    }
}
