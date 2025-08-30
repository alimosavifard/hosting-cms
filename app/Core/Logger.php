<?php
namespace App\Core;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    private static $instance = null;
    private $logger;

    private function __construct(string $channel = 'app')
    {
        $this->logger = new MonologLogger($channel);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../storage/logs/app.log', MonologLogger::DEBUG));
    }

    public static function getInstance(string $channel = 'app'): self
    {
        if (self::$instance === null || self::$instance->logger->getName() !== $channel) {
            self::$instance = new self($channel);
        }
        return self::$instance;
    }

    public function info($message, array $context = [])
    {
        $this->logger->info($message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->logger->warning($message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->logger->error($message, $context);
    }
}
?>