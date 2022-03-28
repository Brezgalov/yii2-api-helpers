<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

use Brezgalov\ApiHelpers\v2\DTO\IRenderFormatterDTO;
use Yii;
use Brezgalov\ApiHelpers\v2\IFormatter;
use yii\base\Component;
use yii\base\ViewContextInterface;

class ViewResultFormatter extends Component implements IFormatter
{
    const RENDER_MODE_DEFAULT = 'render';
    const RENDER_MODE_FILE = 'renderFile';
    const RENDER_MODE_AJAX = 'renderAjax';

    /**
     * @var string[]
     */
    public static $renderMods = [
        self::RENDER_MODE_DEFAULT,
        self::RENDER_MODE_FILE,
        self::RENDER_MODE_AJAX,
    ];

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $view;

    /**
     * @var string
     */
    public $mode = self::RENDER_MODE_DEFAULT;

    /**
     * @var ViewContextInterface|string|array
     */
    public $viewContext;

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function pickRenderMode()
    {
        return in_array($this->mode, self::$renderMods) ? $this->mode : self::RENDER_MODE_DEFAULT;
    }

    /**
     * @return ViewContextInterface
     */
    protected function pickViewContext()
    {
        return $this->viewContext instanceof ViewContextInterface ? $this->viewContext : Yii::createObject($this->viewContext);
    }

    /**
     * @param $service
     * @param $result
     * @return false|mixed
     * @throws \Exception
     */
    public function format($service, $result)
    {
        if (!($result instanceof IRenderFormatterDTO)) {
            return $result;
        }

        if ($this->title) {
            Yii::$app->view->title = $this->title;
        }

        $params = $result->getViewParams();

        $context = $this->pickViewContext();
        $method = $this->pickRenderMode();

        return call_user_func([Yii::$app->view, $method], $this->view, $params, $context);
    }
}