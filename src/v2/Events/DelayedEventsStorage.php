<?php

namespace Brezgalov\ApiHelpers\v2\Events;

use yii\base\Component;

class DelayedEventsStorage extends Component
{
    /**
     * @var IDelayedEvent[]
     */
    protected $events = [];

    /**
     * @var IDelayedEvent[]
     */
    protected $eventsFired = [];

    /**
     * @return IDelayedEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Use this func to delay some code for later use
     *
     * @param IDelayedEvent $event
     * @return $this
     */
    public function delayEvent(IDelayedEvent $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Use your delayed code
     *
     * @return bool
     */
    public function fireEvents()
    {
        foreach ($this->events as $key => $event) {
            $event->run();

            $this->eventsFired[] = $event;
            unset($this->events[$key]);
        }

        return true;
    }

    /**
     * Remove delayed events
     * @return bool
     */
    public function clearEvents()
    {
        $this->events = [];

        return true;
    }
}