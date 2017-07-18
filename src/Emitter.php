<?php
namespace EventIO\InterOp\Symfony;

use EventIO\InterOp\EmitterInterface;
use EventIO\InterOp\EventInterface;
use Shrikeh\Bounce\Event\Named;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Emitter
 * @package EventIO\InterOp\Symfony
 */
class Emitter implements EmitterInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Emitter constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EventDispatcherInterface $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array ...$events The event triggered
     * @return mixed
     */
    public function emit(...$events)
    {
        foreach ($events as $event) {
            $this->parseEvent($event);
        }
    }

    /**
     * @param EventInterface $event The event triggered
     * @return mixed
     */
    public function emitEvent(EventInterface $event)
    {
        $this->dispatcher->dispatch(
            $event->name(),
            Event::fromEvent($event)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function emitName($event)
    {
        return $this->emitEvent($this->createNamedEvent($event));
    }

    /**
     * @param mixed $event
     * @return mixed
     */
    private function parseEvent($event)
    {
        if ($event instanceof EventInterface) {
            return $this->emitEvent($event);
        }

        return $this->emitName($event);
    }

    /**
     * @param string $name
     * @return Named
     */
    private function createNamedEvent(string $name)
    {
        return Named::create($name);
    }
}