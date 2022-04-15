<?php

namespace Brezgalov\ApiHelpers\v2\Events;

use yii\base\Component;

class DelayedEventsStorage extends Component
{
    /**
     * @var IDelayedEvent[]
     */
    protected static $events = [];

    /**
     * @var IDelayedEvent[]
     */
    protected static $eventsFired = [];

    /**
     * @return IDelayedEvent[]
     */
    public static function getEvents()
    {
        return static::$events;
    }

    /**
     * Use this func to delay some code for later use
     *
     * @param IDelayedEvent $event
     * @return bool
     */
    public static function delayEvent(IDelayedEvent $event)
    {
        static::$events[] = $event;

        return true;
    }

    /**
     * Use your delayed code
     *
     * @return bool
     */
    public static function fireEvents()
    {
        foreach (static::$events as $key => $event) {
            $event->run();

            static::$eventsFired[] = $event;
            unset(static::$events[$key]);
        }

        return true;
    }

    /**
     * Remove delayed events
     * @return bool
     */
    public static function clearEvents()
    {
        static::$events = [];

        return true;
    }
}