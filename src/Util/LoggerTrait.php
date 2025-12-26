<?php

// src/Util/LoggerTrait.php
namespace Note\Util;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function info(string $message)
    {
        $this->logger->info($message);
    }

    protected function warn(string $message)
    {
        $this->logger->warning($message);
    }

    protected function error(string $message)
    {
        $this->logger->error($message);
    }
}
