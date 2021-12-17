<?php

namespace Brezgalov\ApiHelpers;

interface IApiMutexHelper
{
    /**
     * @return string
     */
    public function buildActionMutexName();
}