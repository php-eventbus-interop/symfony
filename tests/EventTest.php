<?php
declare(strict_types=1);

namespace EventIO\InterOp\Symfony\Test;

use EventIO\InterOp\Symfony\Event;
use EventIO\InterOp\Symfony\Exception\Event\MethodNotFoundException;
use EventIO\InterOp\Symfony\Exception\Event\PropertyNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

final class EventTest extends TestCase
{
    public function testHasName()
    {
        // arrange
        $eventName = 'foo.bar';
        $testEvent = new SymfonyEvent();

        // act
        $bridgedEvent = Event::fromSymfony($testEvent, $eventName);

        // assert
        $this->assertEquals($bridgedEvent->name(), $eventName);
    }

    public function testCanStopPropogation()
    {
        // arrange
        $testEvent = new SymfonyEvent();
        $eventName = 'foo.bar';
        $bridgedEvent = Event::fromSymfony($testEvent, $eventName);

        // act
        $testEvent->stopPropagation();

        // assert
        $this->assertTrue($bridgedEvent->isPropagationStopped());
    }

    public function testItAllowsAccessToTheWrappedEventMethods()
    {
        // arrange
        $mockEvent = $this->getMockBuilder(SymfonyEvent::class)
            ->setMethods(['getSomeData'])
            ->getMock();

        $returnVal = 'foobarbaz';

        $mockEvent->expects($this->once())->method('getSomeData')
            ->will($this->returnValue($returnVal));
        // act
        $eventName = 'foo.bar';
        $bridgedEvent = Event::fromSymfony($mockEvent, $eventName);

        // assert
        $this->assertSame($bridgedEvent->getSomeData(), $returnVal);
    }

    public function testThrowsAnExceptionIfTheWrappedEventMethodIsNotCallable()
    {
        $testEvent = new SymfonyEvent();
        $eventName = 'foo.bar';
        $bridgedEvent = Event::fromSymfony($testEvent, $eventName);

        $this->expectException(MethodNotFoundException::class);
        $bridgedEvent->doSomething();
    }

    public function testAllowsAccessToTheWrappedEventProperty()
    {
        // arrange
        $mockEvent = $this->getMockBuilder(SymfonyEvent::class)->getMock();
        $val = 'baz';
        $mockEvent->foo = $val;

        $eventName = 'foo.bar';

        // act
        $bridgedEvent = Event::fromSymfony($mockEvent, $eventName);

        // assert
        $this->assertSame($bridgedEvent->foo, $val);
    }

    public function testThrowsAnExceptionIfTheWrappedEventHasNoProperty()
    {
        // arrange
        $testEvent = new SymfonyEvent();
        $eventName = 'foo.bar';
        $bridgedEvent = Event::fromSymfony($testEvent, $eventName);

        $this->expectException(PropertyNotFoundException::class);
        $bridgedEvent->foo;
    }
}