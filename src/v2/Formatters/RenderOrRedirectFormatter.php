<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use yii\base\InvalidConfigException;
use yii\base\Model;

class RenderOrRedirectFormatter extends ViewResultFormatter
{
    /**
     * @var string
     */
    public $redirectUrl;

    /**
     * @param mixed $service
     * @param mixed $result
     * @return false|mixed
     * @throws \Exception
     */
    public function format($service, $result)
    {
        if (empty($this->redirectUrl)) {
            throw new InvalidConfigException('redirect URL should be set');
        }

        if ($result instanceof Model && !$result->hasErrors()) {
            return \Yii::$app->response->redirect($this->redirectUrl);
        }

        return parent::format($service, $result);
    }
}