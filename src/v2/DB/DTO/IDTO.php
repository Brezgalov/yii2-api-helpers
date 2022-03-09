<?php

namespace Brezgalov\ApiHelpers\v2\DB\DTO;

interface IDTO
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param array $data
     * @return bool
     */
    public function loadFromDbData(array $data);

    /**
     * @return array
     */
    public function toArray();
}