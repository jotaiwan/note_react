<?php

// src/Util/LoggerTrait.php
namespace  NoteReact\Util;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    private ?LoggerInterface $logger = null;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    protected function info(string $message): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->info($message);
        }
    }

    protected function warn(string $message): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->warning($message);
        }
    }

    protected function error(string $message): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->error($message);
        }
    }
}
