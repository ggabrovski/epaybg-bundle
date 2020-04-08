<?php

namespace Otobul\EpaybgBundle\Tests;

use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Otobul\EpaybgBundle\OtobulEpaybgBundle;
use Otobul\EpaybgBundle\Service\EpayManagerInterface;

class EpayDecodeRequestTest extends BaseBundleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Make all services public
        $this->addCompilerPass(new PublicServicePass());

        // Make services public that have an idea that matches a regex
        $this->addCompilerPass(new PublicServicePass('|otobul_epaybg.*|'));
    }

    protected function getBundleClass()
    {
        return OtobulEpaybgBundle::class;
    }

    public function testEasypayCodeCreate()
    {
        $this->bootKernel();

        $container = $this->getContainer();
        /** @var EpayManagerInterface $epayManager */
        $epayManager = $container->get('otobul_epaybg.epay_manager');

        // Example ePay.bg response line
        $exampleData = "INVOICE=2:STATUS=PAID:PAY_TIME=20200408022032:STAN=080837:BCODE=080837\n";
        $encoded = base64_encode($exampleData);
        $checksum = $epayManager->getChecksum($encoded);

        $invoiceData = $epayManager->decodeRequest($encoded, $checksum);
        $this->assertIsArray($invoiceData);
        $this->assertCount(1, $invoiceData);
        $this->assertEquals('INVOICE=2:STATUS=OK', $invoiceData[0]);
    }
}
