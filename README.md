# Symfony bundle for ePay.bg

OtobulEpaybgBundle is symfony bundle to help working with ePay.bg communication package for merchants.

## 1. Installation
Install the package with:

```console
composer require otobul/epaybg-bundle
```

If you're *not* using Symfony Flex, you'll also
need to enable the `Otobul\EpaybgBundle\OtobulEpaybgBundle`
in your `config/bundles.php` file and configure the bundle.

## 2. Configuration

Configure the bundle in **packages/otobul_epaybg.yaml**
The default values can be listed with:

```
php bin/console config:dump otobul_epaybg
```
To work properly you need also to configure dev env files. If you don`t have access to ePay.bg demo system you can visit 
https://demo.epay.bg/
and make registration to get demo merchant number and secret keys. 


## 3. Usage

This bundle provides:
- controller to handle ePay.bg webhook notification;
- service that can be used in you console command or custom controller;
- template functions to generate "Pay" button and Easypay payment code directly in your template.

### 3.1 Template 
#### 3.1.1 Generate "Pay" button for WEB_LOGIN form

To generate simple **web_login** "Pay" button in your template you can use `epayWebLoginForm` twig function. Example:
```
{% include '@OtobulEpaybg/Form/web_login.html.twig' with {
    form: epayWebLoginForm({invoice: 1, amount: 100})
} only %}
```
Required parameters:
- **invoice**: Your unique invoice number;
- **amount**: Total sum in BGN. This is the default currency.

Advanced configuration of **web_login** "Pay" button allow you to configure other optional parameters:
```
{% include '@OtobulEpaybg/Form/web_login.html.twig' with {
    form: epayWebLoginForm({
        invoice: 1,
        amount: 100,
        returnUrl: url('your_payment_success_route'),
        cancelUrl: url('your_payment_cancel_route'),
        expDate: expDate,
        currency: 'EUR',
        description: 'Extra description max to 100 symbols',
        encoding: 'utf-8',
    }), button: 'Pay'
} only %}
```
Optional parameters:
- **returnUrl**: Your success route. ePay.bg will redirect the user after successful payment; 
- **cancelUrl**: Your cancel route. ePay.bg will redirect the user after cancel payment; 
- **expDate**: Expiration date. Variable need to be in \DateTime. Default is +7 days;
- **currency**: ISO three-letter currency code. Accepted value are BGN|EUR|USD;
- **description**: Extra description max to 100 symbols;
- **encoding**: Accepted value are utf-8 or CP1251;
- **button**: Label of the pay button.
 
#### 3.1.2 Generate "Pay" button for CREDIT_CARD form 

To generate simple **credit_card** "Pay" button in your template you can use `epayCreditCardForm` twig function. Example:
```
{% include '@OtobulEpaybg/Form/web_login.html.twig' with {
    form: epayCreditCardForm({invoice: 1, amount: 100})
} only %}
```
For advanced configuration you can use the same optional parameter from above. 

#### 3.1.3 Generate "Easypay code" in template

To generate **Easypay code** in your template you can use `epayEasypayCode` twig function. Example:
```
{{ epayEasypayCode({invoice: 1, amount: 100}) }}
```
Please note that this will make HTTP request to Easypay server to retrieve the code. Use this careful! Recommended way is to generate the code in your controller and store it for later use. 


### 3.2 Controller 
#### 3.2.1 Generate "Easypay code" in controller

Example usages of EpayManager service in controller to retrieve Easypay payment code.

```
namespace App\Controller;

use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Otobul\EpaybgBundle\Service\EpayManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    public function generateEasypayCode(EpayManagerInterface $epayManager)
    {
        $invoice = 1; // Generate your unique invoice number
        $amount = 100; // Total sum for payment

        $easypayCode = $epayManager->getEasypayCode(
            new EpayPayloadData($invoice, $amount)
        );

        return $this->render("payment/easypay_code.html.twig", [
            'invoice' => $invoice,
            'easypayCode' => $easypayCode,
        ]);
    }
}
```

#### 3.2.1 Generate "Pay" button for WEB_LOGIN form in controller

Example usages of EpayManager service in controller to generate "Pay" button form.

```
namespace App\Controller;

use Otobul\EpaybgBundle\Model\EpayPayloadData;
use Otobul\EpaybgBundle\Service\EpayManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;

class PaymentController extends AbstractController
{
    public function generateEasypayCode(EpayManagerInterface $epayManager, RouterInterface $router)
    {
        $payload = EpayPayloadData::createFromArray([
            'invoice' => 1,
            'amount' => 100,
        ]);
        $returnUrl = $this->router->generate('your_payment_success_route');
        $cancelUrl = $this->router->generate('your_payment_cancel_route');

        $form = $epayManager->createWebLoginForm($payload, $returnUrl, $cancelUrl);

        return $this->render("payment/easypay_code.html.twig", [
            'form' => $form->createView(),            
        ]);
    }
}
```

To generate **CREDIT_CARD** form use `createCreditCardForm` function.

### 3.3 Webhook notification
To use webhook notification you need to add webhook notification route to your config. Configure the route in **config/routes/otobul_epaybg.yaml**
```
otobul_epaybg:
    resource: '@OtobulEpaybgBundle/Resources/config/routes.xml'
    prefix: /webhook/epaybg
```
The new notification URL will be something like: `https://your-domain.com/webhook/epaybg/`, add the notification URL in your ePay.bg account.


### 3.4 Events

- **NOTIFICATION_RECEIVED**: Called directly after the webhook notification is received. Listeners have the opportunity to get the raw content.
- **NOTIFICATION_ERROR**: Called if webhook notification has not valid checksum or data. Listeners have the opportunity to get the error message.
- **NOTIFICATION_RESPONSE**: Called directly before the webhook notification response to be sent. Listeners have the opportunity to get the raw response content.
- **INVOICE_NOTIFICATION_RECEIVED**: Called directly after the invoice notification is received. Listeners have the opportunity to process the invoice data.
- **EASYPAY_CODE_CREATED**: Called directly after the Easypay code is received. Listeners have the opportunity to get the code and payment data.

Example event subscriber:

```
namespace App\EventSubscriber;

use Otobul\EpaybgBundle\Event\NotificationErrorEvent;
use Otobul\EpaybgBundle\Event\NotificationReceivedEvent;
use Otobul\EpaybgBundle\Event\NotificationResponseEvent;
use Otobul\EpaybgBundle\Event\OtobulEpaybgEvents;
use Otobul\EpaybgBundle\Event\InvoiceNotificationReceivedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EpayInvoiceNotificationSubscriber implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            OtobulEpaybgEvents::NOTIFICATION_RECEIVED => 'epayNotificationReceived',
            OtobulEpaybgEvents::NOTIFICATION_ERROR => 'epayNotificationError',
            OtobulEpaybgEvents::NOTIFICATION_RESPONSE => 'epayNotificationResponse',
            OtobulEpaybgEvents::INVOICE_NOTIFICATION_RECEIVED => 'epayInvoiceNotification',
        ];
    }

    public function epayNotificationReceived(NotificationReceivedEvent $event)
    {
        $this->logger->info('epayNotificationReceived: '. $event->getContent());
    }

    public function epayNotificationError(NotificationErrorEvent $event)
    {
        $this->logger->info('epayNotificationError: '. $event->getMessage());
    }

    public function epayNotificationResponse(NotificationResponseEvent $event)
    {
        $this->logger->info('epayNotificationResponse: '. $event->getContent());
    }

    public function epayInvoiceNotification(InvoiceNotificationReceivedEvent $event)
    {
        $this->logger->info('epayInvoiceNotification: '. $event->getInvoice() .' isPaid: '. $event->isPaid());
    }
}
```
