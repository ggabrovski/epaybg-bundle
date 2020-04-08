<?php

namespace Otobul\EpaybgBundle\Model;

use Otobul\EpaybgBundle\Exception\NotValidPayloadDataException;
use Symfony\Component\Validator\Validation;

class EpayPayloadData
{

    private $invoice;
    private $amount;
    private $expDate;
    private $currency;
    private $description;
    private $encoding;

    /**
     * PaymentData constructor.
     * @param int $invoice
     * @param float $amount
     * @param \DateTime $expDate
     * @param string $currency
     * @param string|null $description
     * @param string $encoding
     * @throws NotValidPayloadDataException
     */
    public function __construct(int $invoice, float $amount, \DateTime $expDate = null, string $currency=null, string $description=null, string $encoding=null)
    {
        $this->invoice = $invoice;
        $this->amount = $amount;
        $this->expDate = $expDate ?? new \DateTime('+7 days');
        $this->currency = $currency ?? 'BGN';
        $this->description = $description ?? '';
        $this->encoding = $encoding ?? 'utf-8';

        $validator = Validation::createValidatorBuilder()
            ->addXmlMapping(__DIR__ .'/../Resources/config/validation.xml')
            ->getValidator();

        $errors = $validator->validate($this);

        if(count($errors) > 0) {
            throw new NotValidPayloadDataException($errors);
        }
    }


    /**
     * @param array $properties
     * @return static
     * @throws NotValidPayloadDataException
     */
    public static function createFromArray(array $properties): self
    {
        $invoice = $properties['invoice'] ?? null;
        $amount = $properties['amount'] ?? null;
        $expDate = $properties['expDate'] ?? null;
        $currency = $properties['currency'] ?? null;
        $description = $properties['description'] ?? null;
        $encoding = $properties['encoding'] ?? null;

        return new self($invoice, $amount, $expDate, $currency, $description, $encoding);
    }

    public function toArray(): array
    {
        return [
            'INVOICE' => $this->getInvoice(),
            'AMOUNT' => sprintf('%.2f', $this->getAmount()),
            'CURRENCY' => $this->getCurrency(),
            'EXP_TIME' => $this->getExpDate()->format('d.m.Y H:i'),
            'DESCR' => $this->getDescription(),
            'ENCODING' => $this->getEncoding(),
        ];
    }

    public function getInvoice(): int
    {
        return $this->invoice;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getExpDate(): \DateTime
    {
        return $this->expDate;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }
}
