<?php

namespace Brezgalov\ApiHelpers;

use yii\db\ActiveQuery;

interface ISearch
{
    const SEARCH_METHOD = 'getQuery';

    /**
     * @return ActiveQuery
     */
    public function getQuery();
}