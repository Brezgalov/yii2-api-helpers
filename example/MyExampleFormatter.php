<?php

namespace example;

use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;

class MyExampleFormatter extends ModelResultFormatter
{
    /**
     * @param object $service
     * @param mixed $result
     * @return mixed|object|\yii\base\Model|\yii\web\Response
     * @throws \Exception
     */
    public function format($service, $result)
    {
        if (is_array($result)) {
            foreach ($result as &$item) {
                unset($item['sex']);
            }
        }

        return parent::format($service, $result);
    }
}