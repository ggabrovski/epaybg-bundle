<?php

namespace Otobul\EpaybgBundle\Twig\Extension;

use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Otobul\EpaybgBundle\Service\EpayManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EpayExtension extends AbstractExtension
{
    private $epayManager;

    public function __construct(EpayManagerInterface $epayManager)
    {
        $this->epayManager = $epayManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('epayWebLoginForm', [$this, 'epayWebLoginFormFunction']),
            new TwigFunction('epayCreditCardForm', [$this, 'epayCreditCardFormFunction']),
            new TwigFunction('epayEasypayCode', [$this, 'epayEasypayCodeFunction']),
        ];
    }

    /**
     * @param array $properties
     * @return \Symfony\Component\Form\FormView
     * @throws \Otobul\EpaybgBundle\Exception\NotValidPayloadDataException
     */
    public function epayWebLoginFormFunction(array $properties)
    {
        $paymentData = EpayPayloadData::createFromArray($properties);
        $returnUrl = $properties['returnUrl'] ?? '';
        $cancelUrl = $properties['cancelUrl'] ?? '';

        return $this->epayManager->createWebLoginFormAndView($paymentData, $returnUrl, $cancelUrl);
    }

    /**
     * @param array $properties
     * @return \Symfony\Component\Form\FormView
     * @throws \Otobul\EpaybgBundle\Exception\NotValidPayloadDataException
     */
    public function epayCreditCardFormFunction(array $properties)
    {
        $paymentData = EpayPayloadData::createFromArray($properties);
        $returnUrl = $properties['returnUrl'] ?? '';
        $cancelUrl = $properties['cancelUrl'] ?? '';

        return $this->epayManager->createCreditCardFormAndView($paymentData, $returnUrl, $cancelUrl);
    }


    /**
     * @param array $properties
     * @return string
     * @throws \Otobul\EpaybgBundle\Exception\NotValidPayloadDataException
     */
    public function epayEasypayCodeFunction(array $properties): string
    {
        $paymentData = EpayPayloadData::createFromArray($properties);

        return $this->epayManager->getEasypayCode($paymentData);
    }
}
