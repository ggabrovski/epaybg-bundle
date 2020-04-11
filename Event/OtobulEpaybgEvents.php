<?php

namespace Otobul\EpaybgBundle\Event;

final class OtobulEpaybgEvents
{
    /**
     * Called directly after the webhook notification is received.
     *
     * Listeners have the opportunity to get the raw content.
     *
     * @Event("Otobul\EpaybgBundle\Event\NotificationReceivedEvent")
     */
    const NOTIFICATION_RECEIVED = 'otobul_epaybg.notification_received';

    /**
     * Called if webhook notification has not valid checksum or data.
     *
     * Listeners have the opportunity to get the error message.
     *
     * @Event("Otobul\EpaybgBundle\Event\NotificationErrorEvent")
     */
    const NOTIFICATION_ERROR = 'otobul_epaybg.notification_error';

    /**
     * Called directly before the webhook notification response to be sent.
     *
     * Listeners have the opportunity to get the raw response content.
     *
     * @Event("Otobul\EpaybgBundle\Event\NotificationResponseEvent")
     */
    const NOTIFICATION_RESPONSE = 'otobul_epaybg.notification_response';

    /**
     * Called directly after the invoice notification is received.
     *
     * Listeners have the opportunity to process the invoice data.
     *
     * @Event("Otobul\EpaybgBundle\Event\InvoiceNotificationReceivedEvent")
     */
    const INVOICE_NOTIFICATION_RECEIVED = 'otobul_epaybg.invoice_notification_received';

    /**
     * Called directly after the Easypay code is received.
     *
     * Listeners have the opportunity to get the code and payment data.
     *
     * @Event("Otobul\EpaybgBundle\Event\EasypayCodeCreatedEvent")
     */
    const EASYPAY_CODE_CREATED = 'otobul_epaybg.easypay_code_created';
}
