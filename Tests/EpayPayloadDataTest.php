<?php

namespace Otobul\EpaybgBundle\Tests;

use Nyholm\BundleTest\BaseBundleTestCase;
use Otobul\EpaybgBundle\Exception\NotValidPayloadDataException;
use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Otobul\EpaybgBundle\OtobulEpaybgBundle;

class EpayPayloadDataTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return OtobulEpaybgBundle::class;
    }

    public function testPaymentDataWithWrongInvoice()
    {
        $this->bootKernel();

        $this->expectException(NotValidPayloadDataException::class);
        $payloadData = new EpayPayloadData(-100, 10, new \DateTime('+1 days'));
    }

    public function testPaymentDataWithWrongAmount()
    {
        $this->bootKernel();

        $this->expectException(NotValidPayloadDataException::class);
        new EpayPayloadData(100, -10, new \DateTime('+1 days'));
    }

    public function testPaymentDataWithWrongExpDate()
    {
        $this->bootKernel();

        $this->expectException(NotValidPayloadDataException::class);
        new EpayPayloadData(100, 10, new \DateTime('-1 days'));
    }

    public function testPaymentData()
    {
        $this->bootKernel();

        $expDate = new \DateTime('+1 days');
        $paymentData = new EpayPayloadData(100, 10, $expDate);
        $this->assertInstanceOf(EpayPayloadData::class, $paymentData);

        $paymentDataAsArray = $paymentData->toArray();

        $this->assertArrayHasKey('INVOICE', $paymentDataAsArray);
        $this->assertArrayHasKey('AMOUNT', $paymentDataAsArray);
        $this->assertArrayHasKey('CURRENCY', $paymentDataAsArray);
        $this->assertArrayHasKey('EXP_TIME', $paymentDataAsArray);
        $this->assertArrayHasKey('DESCR', $paymentDataAsArray);
        $this->assertArrayHasKey('ENCODING', $paymentDataAsArray);

        $this->assertEquals($paymentDataAsArray['EXP_TIME'], $expDate->format('d.m.Y H:i'));
    }
}
