<?php

namespace Otobul\EpaybgBundle\Event;

use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Symfony\Contracts\EventDispatcher\Event;

class EasypayCodeCreatedEvent extends Event
{
    private $paymentData;
    private $code;

    public function __construct(EpayPayloadData $paymentData, string $code)
    {
        $this->paymentData = $paymentData;
        $this->code = $code;
    }

    public function getPaymentData(): EpayPayloadData
    {
        return $this->paymentData;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
