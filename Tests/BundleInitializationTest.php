<?php


namespace Otobul\EpaybgBundle\Tests;

use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Otobul\EpaybgBundle\OtobulEpaybgBundle;
use Otobul\EpaybgBundle\Service\EpayManager;

class BundleInitializationTest extends BaseBundleTestCase
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

    public function testInitBundle()
    {
        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();

        // Test if you services exists
        $this->assertTrue($container->has('otobul_epaybg.epay_manager'));
        $service = $container->get('otobul_epaybg.epay_manager');
        $this->assertInstanceOf(EpayManager::class, $service);
    }
}
