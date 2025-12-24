<?php

namespace Otobul\EpaybgBundle\Model;

use Otobul\EpaybgBundle\Exception\NotValidPayloadDataException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class EpayPayloadData
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range(min: 0)]
    private $invoice;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\Range(min: 0)]
    private $amount;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual('today')]
    private $expDate;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/\b(BGN|EUR|USD)\b/')]
    private $currency;

    #[Assert\Length(min: 0, max: 100)]
    private $description;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/\b(utf-8|CP1251)\b/')]
    private $encoding;

    private $lang;

    /**
     * PaymentData constructor.
     * @param int $invoice
     * @param float $amount
     * @param \DateTime $expDate
     * @param string $currency
     * @param string|null $description
     * @param string $encoding
     * @param string $lang
     * @throws NotValidPayloadDataException
     */
    public function __construct(int $invoice, float $amount, \DateTime $expDate = null, string $currency=null, string $description=null, string $encoding=null, string $lang=null)
    {
        $this->invoice = $invoice;
        $this->amount = $amount;
        $this->expDate = $expDate ?? new \DateTime('+7 days');
        $this->currency = $currency ?? 'EUR';
        $this->description = $description ?? '';
        $this->encoding = $encoding ?? 'utf-8';
        $this->lang = $lang ?? 'bg';

        $validator = Validation::createValidator();

        $errors = $validator->validate($this);

        if (count($errors) > 0) {
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
        $lang = $properties['lang'] ?? null;

        return new self($invoice, $amount, $expDate, $currency, $description, $encoding, $lang);
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
            'LANG' => $this->getLang(),
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

    public function getLang(): string
    {
        return $this->lang;
    }
}
