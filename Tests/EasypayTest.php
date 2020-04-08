<?php

namespace Otobul\EpaybgBundle\Tests;

use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Otobul\EpaybgBundle\Exception\EasypayGetCodeException;
use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Otobul\EpaybgBundle\OtobulEpaybgBundle;
use Otobul\EpaybgBundle\Service\EpayManagerInterface;

class EasypayTest extends BaseBundleTestCase
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

        $payloadData = new EpayPayloadData(100, 10, new \DateTime('+1 days'));

        $this->expectException(EasypayGetCodeException::class);
        $epayManager->getEasypayCode($payloadData);
    }

}
