<?php

namespace Otobul\EpaybgBundle\Controller;

use Otobul\EpaybgBundle\Event\NotificationErrorEvent;
use Otobul\EpaybgBundle\Event\NotificationReceivedEvent;
use Otobul\EpaybgBundle\Event\NotificationResponseEvent;
use Otobul\EpaybgBundle\Event\OtobulEpaybgEvents;
use Otobul\EpaybgBundle\Service\EpayManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class WebhookController
{
    private $epayManager;

    private $eventDispatcher;
    private $requestStack;

    public function __construct(EpayManager $epayManager, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->epayManager = $epayManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    public function __invoke()
    {
        $request = $this->requestStack->getCurrentRequest();
        $content = $request->getContent();

        if ($this->eventDispatcher) {
            $event = new NotificationReceivedEvent($content);
            $this->eventDispatcher->dispatch($event, OtobulEpaybgEvents::NOTIFICATION_RECEIVED);
        }

        try {
            $lines = $this->epayManager->decodeRequest($request->request->get('encoded'), $request->request->get('checksum'));
        } catch (\Exception $e) {
            if ($this->eventDispatcher) {
                $event = new NotificationErrorEvent($e->getMessage());
                $this->eventDispatcher->dispatch($event, OtobulEpaybgEvents::NOTIFICATION_ERROR);
            }
            return new Response('The request is not valid!', 400);
        }

        $responseContent = implode("\n", $lines);
        if ($this->eventDispatcher) {
            $event = new NotificationResponseEvent($responseContent);
            $this->eventDispatcher->dispatch($event, OtobulEpaybgEvents::NOTIFICATION_RESPONSE);
        }

        return new Response($responseContent);
    }
}
