<?php

namespace Otobul\EpaybgBundle\Service;

use Otobul\EpaybgBundle\Exception\EasypayGetCodeException;
use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

interface EpayManagerInterface
{
    public function createWebLoginForm(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormInterface;
    public function createWebLoginFormAndView(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormView;

    public function createCreditCardForm(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormInterface;
    public function createCreditCardFormAndView(EpayPayloadData $paymentData, string $returnUrl, string $cancelUrl): FormView;

    /**
     * @param EpayPayloadData $paymentData
     * @return string
     * @throws EasypayGetCodeException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getEasypayCode(EpayPayloadData $paymentData): string;

    public function isValidRequest(string $encoded, string $checksum): bool;
    public function decodeRequest(string $encoded, string $checksum): array;

    public function getEncoded(array $payload): string;
    public function getChecksum(string $encoded): string;
}
