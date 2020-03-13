<?php

namespace App\EventSubscriber;

use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $to;
    private $from;

    public function __construct(\Swift_Mailer $mailer, string $from = 'default@domain.fr', string $to = 'default@domain.fr')
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->to = $to;
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event)
    {
        $message = (new Swift_Message())
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setBody("{$event->getRequest()->getRequestUri()}
            
            {$event->getException()->getMessage()}
            
            {$event->getException()->getTraceAsString()}")
        ;

        $this->mailer->send($message);
    }
}
