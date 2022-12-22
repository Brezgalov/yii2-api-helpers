<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\ErrorException;
use Brezgalov\ApiHelpers\v2\IFormatter;
use yii\base\Component;
use yii\base\Model;
use yii\web\Response;

class ModelResultFormatter extends Component implements IFormatter
{
    /**
     * @var string
     */
    public $unknownExecutionErrorText = 'Unknown error occurred';

    /**
     * @var Response
     */
    public $response;

    /**
     * ApiHelpersLibResultFormatter constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if (empty($this->response) && \Yii::$app->has('response')) {
            $this->response = \Yii::$app->get('response');
        }
    }

    /**
     * @param object $service
     * @param mixed $result
     * @return mixed
     * @throws \Exception
     */
    public function format($service, $result)
    {
        if ($result instanceof ErrorException && $this->response) {
            $response = clone $this->response;

            $response->data = $result->error;
            $response->setStatusCode($result->statusCode);

            return $response;
        }

        if ($result instanceof \Exception) {
            throw $result;
        }

        if ($result === false) {
            $errorModel = $service;

            if ($service && $errorModel instanceof Model) {
                if (!$service->hasErrors()) {
                    $service->addError(static::class, $this->unknownExecutionErrorText);
                }
            } else {
                $errorModel = new Model();
                $service->addError(static::class, $this->unknownExecutionErrorText);
            }

            $result = $errorModel;
        }

        return $result;
    }
}
