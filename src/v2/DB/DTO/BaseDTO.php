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

    /**
     * @param array $data
     * @return bool|void
     */
    public function loadFromDbData(array $data)
    {
        $this->id = (int)$data['id'];
    }

    /**
     * @return int[]
     */
    public function toArray()
    {
        return ['id' => $this->id];
    }
}