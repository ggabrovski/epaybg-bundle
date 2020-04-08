<?php

namespace Otobul\EpaybgBundle\Service;

use Otobul\EpaybgBundle\Event\EasypayCodeCreatedEvent;
use Otobul\EpaybgBundle\Event\InvoiceNotificationReceivedEvent;
use Otobul\EpaybgBundle\Event\OtobulEpaybgEvents;
use Otobul\EpaybgBundle\Exception\EasypayGetCodeException;
use Otobul\EpaybgBundle\Exception\NotEncodedOrChecksumException;
use Otobul\EpaybgBundle\Exception\NotificationNoDataException;
use Otobul\EpaybgBundle\Exception\NotValidChecksumException;
use Otobul\EpaybgBundle\Form\EpayButtonType;
use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class EpayManager implements EpayManagerInterface
{
    private const PAYMENT_TYPE_WEB_LOGIN = 'paylogin';
    private const PAYMENT_TYPE_CREDIT_CARD = 'credit_paydirect';

    private $eventDispatcher;
    private $min;
    private $secret;
    private $isDemo;

    public function __construct(EventDispatcherInterface $eventDispatcher, string $min, string $secret, bool $isDemo)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->min = $min;
        $this->secret = $secret;
        $this->isDemo = $isDemo;
    }

    public function createWebLoginFormAndView(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormView
    {
        return $this->createWebLoginForm($paymentData, $returnUrl, $cancelUrl)->createView();
    }

    public function createWebLoginForm(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormInterface
    {
        return $this->createForm($paymentData, $returnUrl, $cancelUrl, self::PAYMENT_TYPE_WEB_LOGIN);
    }

    public function createCreditCardFormAndView(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormView
    {
        return $this->createCreditCardForm($paymentData, $returnUrl, $cancelUrl)->createView();
    }

    public function createCreditCardForm(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormInterface
    {
        return $this->createForm($paymentData, $returnUrl, $cancelUrl, self::PAYMENT_TYPE_CREDIT_CARD);
    }

    public function getEasypayCode(EpayPayloadData $paymentData): string
    {
        $payload = $paymentData->toArray();
        $payload['MIN'] = $this->min;

        $encoded = $this->getEncoded($payload);
        $checksum = $this->getChecksum($encoded);

        $client = HttpClient::create();
        $response = $client->request('GET', $this->getEndpointEasypay(), [
            'query' => [
                'encoded' => $encoded,
                'checksum' => $checksum,
            ],
        ]);

        $content = $response->getContent();
        if(preg_match('/^IDN=(.+)/', $content, $match)) {
            $code = $match[1];
            if ($this->eventDispatcher) {
                $event = new EasypayCodeCreatedEvent($paymentData, $code);
                $this->eventDispatcher->dispatch($event, OtobulEpaybgEvents::EASYPAY_CODE_CREATED);
            }
            return $code;
        }

        throw new EasypayGetCodeException($paymentData, $content);
    }

    public function isValidRequest(string $encoded, string $checksum): bool
    {
        return $checksum == $this->getChecksum($encoded);
    }

    public function decodeRequest(string $encoded, string $checksum): array
    {
        if(empty($encoded) || empty($checksum)) {
            throw new NotEncodedOrChecksumException();
        }

        if(!$this->isValidRequest($encoded, $checksum)) {
            throw new NotValidChecksumException();
        }

        $data = base64_decode($encoded);
        $lines_arr = explode("\n", $data);
        $info_data = [];

        foreach ($lines_arr as $line) {
            if (preg_match("/^INVOICE=(\d+):STATUS=(PAID|DENIED|EXPIRED)(:PAY_TIME=(\d+):STAN=(\d+):BCODE=([0-9a-zA-Z]+))?$/", $line, $regs)) {
                $invoice  = $regs[1];
                $status   = $regs[2];
                $pay_date = $regs[4] ?? null; # XXX if PAID
                $stan     = $regs[5] ?? null; # XXX if PAID
                $bcode    = $regs[6] ?? null; # XXX if PAID

                $responseStatus = InvoiceNotificationReceivedEvent::RESPONSE_STATUS_ERROR;
                # XXX process $invoice, $status, $pay_date, $stan, $bcode here
                if ($this->eventDispatcher) {
                    $event = new InvoiceNotificationReceivedEvent($invoice, $status, $pay_date, $stan, $bcode);
                    $this->eventDispatcher->dispatch($event, OtobulEpaybgEvents::INVOICE_NOTIFICATION_RECEIVED);
                    $responseStatus = $event->getResponseStatus();
                }

                $info_data[] = "INVOICE=$invoice:STATUS={$responseStatus}";
            }
        }

        if(count($info_data) <= 0) {
            throw new NotificationNoDataException();
        }

        return $info_data;
    }

    private function createForm(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl, string $paymentType): FormInterface
    {
        $payload = $paymentData->toArray();
        $payload['MIN'] = $this->min;

        $encoded = $this->getEncoded($payload);
        $checksum = $this->getChecksum($encoded);

        $params = [
            'PAGE' => $paymentType,
            'ENCODED' => $encoded,
            'CHECKSUM' => $checksum,
            'URL_OK' => $returnUrl,
            'URL_CANCEL' => $cancelUrl
        ];

        return Forms::createFormFactory()->create(EpayButtonType::class, null, [
            'params' => $params,
            'actionUrl' => $this->getEndpointEpay(),
        ]);
    }

    private function getEndpointEasypay(): string
    {
        return $this->isDemo ? 'https://demo.epay.bg/ezp/reg_bill.cgi' : 'https://www.epay.bg/ezp/reg_bill.cgi';
    }

    private function getEndpointEpay(): string
    {
        return $this->isDemo ? 'https://demo.epay.bg/' : 'https://www.epay.bg/';
    }

    public function getEncoded(array $payload): string
    {
        $data_array = array_keys($payload);
        array_walk($data_array, function(&$value, $key, $values) {
            $value = !empty($values[$key]) ? $value . '=' . $values[$key] : false;
        }, array_values($payload));

        $imploded = implode(PHP_EOL, array_filter($data_array));

        return base64_encode($imploded);
    }

    public function getChecksum(string $encoded): string
    {
        return $this->hmac($encoded, $this->secret);
    }

    private function hmac($data, $secret, $algorithm = 'SHA1')
    {
        $algo = strtolower($algorithm);

        $pack = [
            'md5' => 'H32',
            'sha1' => 'H40'
        ];

        if (\strlen($secret) > 64) {
            $secret = \pack($pack[$algo], $algo($secret));
        }
        if (\strlen($secret) < 64) {
            $secret = str_pad($secret, 64, chr(0));
        }

        $ipad = \substr($secret, 0, 64) ^ str_repeat(chr(0x36), 64);
        $opad = \substr($secret, 0, 64) ^ str_repeat(chr(0x5C), 64);

        return ($algo($opad . \pack($pack[$algo], $algo($ipad . $data))));
    }


}
