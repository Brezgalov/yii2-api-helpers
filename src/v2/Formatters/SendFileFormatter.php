<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\Formatters\ModelResultFormatter;
use Brezgalov\ApiHelpers\v2\IFormatter;
use yii\base\InvalidArgumentException;
use yii\web\Response;
use Exception;

class SendFileFormatter extends ModelResultFormatter implements IFormatter
{
    /**
     * @var Response
     */
    public $response;

    /**
     * @var bool
     */
    public $dropOnSend = false;

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
        if ($result === false || $result instanceof Exception) {
            return parent::format($service, $result);
        }

        if (!is_string($result)) {
            throw new InvalidArgumentException('Service $result value supposed to be string');
        }

        if (!is_file($result)) {
            throw new InvalidArgumentException("Can not find file \"{$result}\"");
        }

        $response = clone $this->response;
        if ($this->dropOnSend) {
            $response->on(Response::EVENT_AFTER_SEND, function ($event) {
                if (is_file($event->data['unlinkFile'])) {
                    unlink($event->data['unlinkFile']);
                }
            }, ['unlinkFile' => $result]);
        }

        $fileName = null;
        if ($this->attachmentName) {
            $fileName = is_callable($this->attachmentName) ? (
                call_user_func($this->attachmentName, $service, $result)
            ) : $this->attachmentName;
        }

        return $response->sendFile($result, $fileName, $this->sendOptions);
    }
}