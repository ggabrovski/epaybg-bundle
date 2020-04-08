<?php

namespace Otobul\EpaybgBundle\Exception;

class NotValidPayloadDataException extends \Exception
{
    protected $errors;

    public function __construct($errors) {
        $this->errors = $errors;
        parent::__construct();
    }

    public function __toString() {
        return 'Provided payment data are not valid: ' . $this->errors;
    }
}
