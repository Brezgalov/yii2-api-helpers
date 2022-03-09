<?php

namespace Brezgalov\ApiHelpers\v2\Behaviors\Action;

use Brezgalov\ApiHelpers\v2\Action;
use yii\base\Behavior;
use yii\db\Connection;
use yii\db\Transaction;
use Yii;

class TransactionBehavior extends Behavior
{
    /**
     * @var Connection
     */
    public $db;

    /**
     * @var Transaction
     */
    protected $trans;

    /**
     * TransactionBehavior constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if (empty($this->db) && Yii::$app->has('db')) {
            $this->db = Yii::$app->get('db');
        }
    }

    /**
     * @return string[]
     */
    public function events()
    {
        return [
            Action::EVENT_BEFORE_METHOD => 'start',
            Action::EVENT_ON_EXCEPTION => 'rollBack',
            Action::EVENT_ON_FAIL => 'rollBack',
            Action::EVENT_ON_SUCCESS => 'flush',
        ];
    }

    public function start()
    {
        $this->trans = $this->db->beginTransaction();
    }

    public function flush()
    {
        if ($this->trans) {
            $this->trans->commit();
        }
    }

    public function rollBack()
    {
        if ($this->trans) {
            $this->trans->rollBack();
        }
    }
}