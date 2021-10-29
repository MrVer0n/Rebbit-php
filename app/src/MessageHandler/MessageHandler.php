<?php

namespace App\MessageHandler;

use App\Message\Message;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MessageHandler implements MessageHandlerInterface
{
    private LoggerInterface $logger;
    private EntityManagerInterface $em;
    
    public function __construct(LoggerInterface $logger, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->logger = $logger;
    }
    
    public function __invoke(Message $message)
    {
        $body = json_decode($message->getData(), true);
        if(json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error('Не верный json!');
            return;
        }
        if(!array_key_exists('command', $body)) {
            $this->logger->error('Command not found');
            return;
        }
        $command = $body['command'];

        if(!array_key_exists('data', $body)) {
            $this->logger->error('Data not found');
            return;
        }
        $data = $body['data'];

        $this->logger->info('Accepted message: '.$body['command']);
        $service = new UserService($this->em);
        switch ($command) {
            case 'createUser':
                $result = $service->createUser($data);
                $this->logger->info(json_encode($result, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
                break;
            case 'update':
                if(!array_key_exists('id',$data)){
                    $this->logger->error('Id not found');
                    return;
                }
                $result = $service->updateUser($data,$data['id']);
                $this->logger->info(json_encode($result, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
                break;
            case 'delete':
                if(!array_key_exists('id',$data)){
                    $this->logger->error('Id not found');
                    return;
                }
                $result = $service->deleteUser($data,$data['id']);
                $this->logger->info(json_encode($result, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
                break;
        }

    }
}
