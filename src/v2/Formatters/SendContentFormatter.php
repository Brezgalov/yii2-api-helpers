<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;
use Brezgalov\ApiHelpers\v2\IFormatter;
use yii\web\Response;

class SendContentFormatter extends ModelResultFormatter implements IFormatter
{
    /**
     * @var Response
     */
    public $response;

    /**
     * @var string|callable
     */
    public $attachmentName;

    /**
     * @var array
     */
    public $sendOptions = [];

    /**
     * SendFileFormatter constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if (empty($this->response) && \Yii::$app->has('response')) {
            $this->response = \Yii::$app->response;
        }
    }

    /**
     * @param object $service
     * @param mixed $result
     * @return \yii\base\Model|Response
     */
    public function format($service, $result)
    {
        if ($result === false) {
            return parent::format($service, $result);
        }

        $response = clone $this->response;

        $fileName = null;
        if ($this->attachmentName) {
            $fileName = is_callable($this->attachmentName) ? (
            call_user_func($this->attachmentName, $service, $result)
            ) : $this->attachmentName;
        }

        return $response->sendContentAsFile($result, $fileName, $this->sendOptions);
    }
}