<?php

namespace Otobul\EpaybgBundle\Exception;

class NotificationNoDataException extends \Exception
{
    public function __toString() {
        return 'Invoice notification no data!';
    }
}
