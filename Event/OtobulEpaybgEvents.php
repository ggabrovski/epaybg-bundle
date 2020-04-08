<?php

namespace Otobul\EpaybgBundle\Event;

final class OtobulEpaybgEvents
{
    /**
     * Called directly after the Easypay code is received.
     *
     * Listeners have the opportunity to get the code and payment data.
     *
     * @Event("Otobul\EpaybgBundle\Event\EasypayCodeCreatedEvent")
     */
    const EASYPAY_CODE_CREATED = 'otobul_epaybg.easypay_code_created';

    /**
     * Called directly after the webhook notification is received.
     *
     * Listeners have the opportunity to get the raw content.
     *
     * @Event("Otobul\EpaybgBundle\Event\NotificationReceivedEvent")
     */
    const NOTIFICATION_RECEIVED = 'otobul_epaybg.notification_received';

    /**
     * Called directly after the invoice notification is received.
     *
     * Listeners have the opportunity to process the invoice data.
     *
     * @Event("Otobul\EpaybgBundle\Event\InvoiceNotificationReceivedEvent")
     */
    const INVOICE_NOTIFICATION_RECEIVED = 'otobul_epaybg.invoice_notification_received';
}
