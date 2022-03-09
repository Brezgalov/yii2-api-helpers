<?php

namespace Brezgalov\ApiHelpers\v2\Events;

interface IDelayedEvent
{
    public function run();
}