<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;

class RenderAction extends BaseAction
{
    /**
     * @var IFormatter
     */
    public $formatter = ModelResultFormatter::class;
}