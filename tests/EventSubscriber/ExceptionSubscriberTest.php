<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ExceptionSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @internal
 * @coversNothing
 */
class ExceptionSubscriberTest extends KernelTestCase
{
    public function testEventSubscription()
    {
        $this->assertArrayHasKey(ExceptionEvent::class, ExceptionSubscriber::getSubscribedEvents());
    }

    public function testOnExceptionSendEmail()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()->getMock();

        $subscriber = new ExceptionSubscriber($mailer, 'from@domain.fr', 'to@domain.fr');
        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new ExceptionEvent($kernel, new Request(), 1, new \Exception());
        $mailer->expects($this->once())->method('send');
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);

        //$subscriber->onException($event);
    }

    public function testOnExceptionSendEmailToTheRightPerson()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()->getMock();

        $mailer->expects($this->once())->method('send')->with($this->callback(function (\Swift_Message $actual_message) {
            return array_key_exists('from@domain.fr', $actual_message->getFrom()) && array_key_exists('to@domain.fr', $actual_message->getTo());
        }));

        $this->dispatch($mailer);
    }

    public function testOnExceptionSendTheRightContent()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()->getMock();

        $mailer->expects($this->once())->method('send')->with($this->callback(function (\Swift_Message $actual_message) {
            return strpos($actual_message->getBody(), 'ExceptionSubscriberTest');
        }));
        $this->dispatch($mailer);
    }

    public function dispatch($mailer)
    {
        $subscriber = new ExceptionSubscriber($mailer, 'from@domain.fr', 'to@domain.fr');
        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new ExceptionEvent($kernel, new Request(), 1, new \Exception());
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }
}
