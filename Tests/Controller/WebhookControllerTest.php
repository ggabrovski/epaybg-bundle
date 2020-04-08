<?php

namespace Otobul\EpaybgBundle\Tests\Controller;

use Nyholm\BundleTest\BaseBundleTestCase;
use Otobul\EpaybgBundle\OtobulEpaybgBundle;
use Symfony\Component\Routing\RouteCollectionBuilder;

class WebhookControllerTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return OtobulEpaybgBundle::class;
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import(__DIR__.'/../../Resources/config/routes.xml', '/webhook');
    }

    public function testIndex()
    {
        $this->bootKernel();

        // TODO: Make webhook controller test
        $this->assertSame(200, 200);
    }

}
