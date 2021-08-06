<?php

namespace Brezgalov\ApiHelpers;

use yii\db\ActiveQuery;

interface ISearch
{
    /**
     * @return ActiveQuery
     */
    public function getQuery();
}