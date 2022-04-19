<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\IFormatter;
use Yii;
use yii\base\Component;

class RedirectBackFormatter extends Component implements IFormatter
{
    /**
     * @var IFormatter
     */
    public $errorFormatter;

    /**
     * RedirectBackFormatter constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->errorFormatter = new ModelResultFormatter();

        parent::__construct($config);
    }

    /**
     * @param $service
     * @param $result
     * @return mixed|object|\yii\base\Model|\yii\web\Response
     * @throws \Exception
     */
    public function format($service, $result)
    {
        if ($result) {
            return Yii::$app->response->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        return $this->errorFormatter->format($service, $result);
    }
}