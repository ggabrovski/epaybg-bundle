<?php

namespace Otobul\EpaybgBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class InvoiceNotificationReceivedEvent extends Event
{
    public const RESPONSE_STATUS_OK = 'OK';
    public const RESPONSE_STATUS_ERROR = 'ERR';
    public const RESPONSE_STATUS_NO = 'NO';

    public const STATUS_PAID = 'PAID';
    public const STATUS_DENIED = 'DENIED';
    public const STATUS_EXPIRED = 'EXPIRED';

    private $invoice;
    private $status;
    private $pay_date;
    private $stan;
    private $bcode;
    private $responseStatus = self::RESPONSE_STATUS_OK;

    public function __construct(int $invoice, string $status, ?int $pay_date, ?int $stan, ?string $bcode)
    {
        $this->invoice = $invoice;
        $this->status = $status;
        $this->pay_date = $pay_date;
        $this->stan = $stan;
        $this->bcode = $bcode;
    }

    public function getInvoice(): int
    {
        return $this->invoice;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPayDate(): ?int
    {
        return $this->pay_date;
    }

    public function getStan(): ?int
    {
        return $this->stan;
    }

    public function getBcode(): ?string
    {
        return $this->bcode;
    }

    public function getResponseStatus(): string
    {
        return $this->responseStatus;
    }

    public function setResponseStatusOk(): void
    {
        $this->responseStatus = self::RESPONSE_STATUS_OK;
    }

    public function setResponseStatusError(): void
    {
        $this->responseStatus = self::RESPONSE_STATUS_ERROR;
    }

    public function setResponseStatusNo(): void
    {
        $this->responseStatus = self::RESPONSE_STATUS_NO;
    }

    public function isPaid(): bool
    {
        return $this->getStatus() === self::STATUS_PAID;
    }

}
