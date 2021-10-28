<?php

namespace App\Message;

final class PingMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    private $payload;

    public function __construct(string $payload)
    {
        $this->payload = $payload;
    }

   public function getPayload(): string
   {
       return $this->payload;
   }
}
