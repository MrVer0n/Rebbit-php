<?php

namespace App\Message;

final class Message
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    private $data;

    public function __construct(string $payload)
    {
        $this->data = $payload;
    }

   public function getData(): string
   {
       return $this->data;
   }
}
