<?php

namespace Brezgalov\ApiHelpers\v2\DB\DTO;

abstract class BaseDTO implements IDTO
{
    /**
     * @var int
     */
    public $id;

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }
}