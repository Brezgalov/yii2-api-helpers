<?php

namespace Brezgalov\ApiHelpers\v2;

use yii\base\Module;

interface ILoadFromModule
{
    public function loadFromModule(Module $module);
}

