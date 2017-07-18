<?php
namespace EventIO\InterOp\Symfony;

use EventIO\InterOp\EventInterface;
use EventIO\InterOp\Symfony\Exception\Event\MethodNotFoundException;
use EventIO\InterOp\Symfony\Exception\Event\PropertyNotFoundException;
use Symfony\Component\EventDispatcher\Event as SymfonyEvent;

/**
 * Class Event
 * @package EventIO\InterOp\Symfony
 */
class Event extends SymfonyEvent implements EventInterface
{

    /**
     * @var EventInterface|SymfonyEvent
     */
    private $wrappedEvent;

    /**
     * @param EventInterface $event
     * @return Event
     */
    public static function fromEvent(EventInterface $event)
    {
        return new self($event, $event->name());
    }

    /**
     * @param SymfonyEvent $event
     * @param string $name
     * @return Event
     */
    public static function fromSymfony(SymfonyEvent $event, string $name)
    {
        return new self($event, $name);
    }

    /**
     * Event constructor.
     * @param $event
     * @param $name
     */
    private function __construct($event, $name)
    {
        $this->wrappedEvent = $event;
        $this->name         = $name;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws MethodNotFoundException
     */
    public function __call($method, $arguments)
    {
        if (!is_callable([$this->wrappedEvent, $method])) {
            $msg = 'The event %s has no method "%s"';
            throw new MethodNotFoundException(sprintf(
                $msg,
                get_class($this->wrappedEvent),
                $method
            ));
        }

        return call_user_func_array([$this->wrappedEvent, $method], $arguments);
    }

    /**
     * @param string $property
     * @return mixed
     * @throws PropertyNotFoundException
     */
    public function __get(string $property)
    {
        if (!property_exists($this->wrappedEvent, $property)) {
            $msg = 'The event %s has no property "%s"';
            throw new PropertyNotFoundException(sprintf(
                $msg,
                get_class($this->wrappedEvent),
                $property
            ));
        }

        return $this->wrappedEvent->$property;
    }

    /**
     * The name of the event
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function stopPropagation()
    {
        $this->wrappedEvent->stopPropagation();
    }

    public function isPropagationStopped()
    {
        return $this->wrappedEvent->isPropagationStopped();
    }

}