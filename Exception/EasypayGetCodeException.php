<?php

namespace Otobul\EpaybgBundle\Exception;

use Otobul\EpaybgBundle\Model\EpayPayloadData;

class EasypayGetCodeException extends \Exception
{
    protected $paymentData;
    protected $response;

    public function __construct(EpayPayloadData $paymentData, string $response) {
        $this->paymentData = $paymentData;
        $this->response = $response;
        parent::__construct();
    }

    public function __toString() {
        return sprintf('Easypay get code failed! Response: %s; Payload: %s', $this->getResponse(), $this->getPaymentData());
    }

    public function getPaymentData(): EpayPayloadData
    {
        return $this->paymentData;
    }

    public function getResponse(): string
    {
        return $this->response;
    }
}
