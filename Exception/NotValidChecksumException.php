<?php

namespace Otobul\EpaybgBundle\Exception;

class NotValidChecksumException extends \Exception
{
    public function __toString() {
        return 'Not valid checksum!';
    }
}
