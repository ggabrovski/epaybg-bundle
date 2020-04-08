<?php

namespace Otobul\EpaybgBundle\Exception;

class NotEncodedOrChecksumException extends \Exception
{
    public function __toString() {
        return 'Missing encoded or checksum fields in request!';
    }
}
