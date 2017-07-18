<?php
declare(strict_types=1);

namespace EventIO\InterOp\Symfony\Test;

use EventIO\InterOp\Symfony\Emitter;
use EventIO\InterOp\Symfony\Event;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Shrikeh\Bounce\Event\Named;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherTest extends TestCase
{
    public function testCanAcceptEventIOEvents()
    {
        // arrange
        $eventName = 'test.event';
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $event      = Named::create($eventName);
        $emitter    = new Emitter($eventDispatcher->reveal());

        // act
        $emitter->emit($event);

        // assert
        $eventDispatcher->dispatch($eventName, Argument::type(Event::class))->shouldHaveBeenCalled();
    }


}