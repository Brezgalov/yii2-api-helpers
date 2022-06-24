<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\IFormatter;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\rest\Serializer;
use yii\web\Response;

/**
 * Class RestFormatter
 * Добавляет \yii\rest\Serializer к отформатированному ответу
 * Может быть использован для добавления API action'ов в контроллер не наследованный от \yii\rest\Controller
 *
 * @package admin\modules\RightsManager\Services
 */
class RestFormatter extends Component  implements IFormatter
{
    /**
     * @var string|array
     */
    public $parentFormatter = ModelResultFormatter::class;

    /**
     * @var string|array
     */
    public $serializer = Serializer::class;

    /**
     * @param object $service
     * @param mixed $result
     * @return Response
     * @throws \yii\base\InvalidConfigException
     */
    public function format($service, $result)
    {
        /** @var IResultFormatter $parent */
        $parent = \Yii::createObject($this->parentFormatter);

        /** @var Response $response */
        $response = $parent->format($service, $result);

        if (!($response instanceof Response)) {
            throw new InvalidConfigException("Returned value by parent supposed to be " . Response::class);
        }

        /** @var Serializer $restSerializer */
        $restSerializer = \Yii::createObject($this->serializer);
        $restSerializer->response = $response;
        $response->data = $restSerializer->serialize($response->data);

        return $response;
    }
}