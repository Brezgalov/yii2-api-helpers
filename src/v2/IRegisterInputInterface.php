<?php

namespace Brezgalov\ApiHelpers\v2;

interface IRegisterInputInterface
{
    /**
     * @param array $data
     * @return bool
     */
    public function registerInput(array $data = []);
}