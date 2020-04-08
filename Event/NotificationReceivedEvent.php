<?php

namespace Otobul\EpaybgBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotificationReceivedEvent extends Event
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

}
