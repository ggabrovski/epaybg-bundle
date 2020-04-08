<?php

namespace Otobul\EpaybgBundle\Controller;

use Otobul\EpaybgBundle\Event\NotificationReceivedEvent;
use Otobul\EpaybgBundle\Event\OtobulEpaybgEvents;
use Otobul\EpaybgBundle\Service\EpayManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class WebhookController
{
    private $epayManager;

    private $eventDispatcher;
    private $requestStack;
    private $logger;

    public function __construct(EpayManager $epayManager, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher = null, LoggerInterface $logger = null)
    {
        $this->epayManager = $epayManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public function index()
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
            if($this->logger) {
                $this->logger->error($e->getMessage());
            }
            return new Response('The request is not valid!', 400);
        }

        if($this->logger) {
            $this->logger->error('WebhookController Response: '.implode("\n", $lines));
        }

        return new Response(implode("\n", $lines));
    }
}
