<?php

namespace Otobul\EpaybgBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotificationErrorEvent extends Event
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

}
