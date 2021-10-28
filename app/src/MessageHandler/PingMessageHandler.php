<?php

namespace App\MessageHandler;

use App\Message\PingMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PingMessageHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function __invoke(PingMessage $message)
    {
        $this->logger->info('Accepted Ping message');
        $this->logger->info("Payload: {$message->getPayload()}");
    }
}
