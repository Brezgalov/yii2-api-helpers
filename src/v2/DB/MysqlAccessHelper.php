<?php

namespace Brezgalov\ApiHelpers\v2\DB;

use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

abstract class MysqlAccessHelper implements IDataAccessHelper
{
    /**
     * @return string
     */
    public function getPrimaryKeyName()
    {
        return 'id';
    }

    /**
     * @return string
     */
    public abstract function getTable();

    /**
     * @return Query
     */
    public function query()
    {
        return (new Query())
            ->select('*')
            ->from(static::getTable());
    }

    /**
     * @param array $columns
     * @return array|false|mixed
     * @throws \Exception
     */
    public function insert(array $columns)
    {
        $db = \Yii::$app->db;
        $res = $db->schema->insert($this->getTable(), $columns);

        return is_array($res) ? ArrayHelper::getValue($res, $this->getPrimaryKeyName()) : $res;
    }

    /**
     * @param $condition - можно передать id как есть, он превратится в ['id' => $condition]
     * @param array $columns
     * @return int
     */
    public function update($condition, array $columns)
    {
        $db = \Yii::$app->db;

        if (is_integer($condition) || is_string($condition)) {
            $condition = [$this->getPrimaryKeyName() => $condition];
        }

        return $db->createCommand()->update($this->getTable(), $columns, $condition)->execute();
    }

    /**
     * @param array|string|null $condition
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($condition = null)
    {
        $db = \Yii::$app->db;

        if ($condition) {
            return $db->createCommand()->delete($this->getTable(), $condition)->execute();
        } else {
            return $db->createCommand("DELETE FROM " . $this->getTable())->execute();
        }
    }
}