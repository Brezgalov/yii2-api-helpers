<?php

namespace Brezgalov\ApiHelpers\v2;

use Brezgalov\ApiHelpers\v2\Formatters\ViewResultFormatter;
use yii\base\ViewContextInterface;

class RenderAction extends BaseAction
{
    /**
     * @var bool
     */
    public $layout = true;

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
    public $mode = ViewResultFormatter::RENDER_MODE_DEFAULT;

    /**
     * @var ViewContextInterface|string|array
     */
    public $viewContext;

    /**
     * @var IFormatter
     */
    public $formatter = ViewResultFormatter::class;

    /**
     * @return ViewResultFormatter|IFormatter|mixed|null
     */
    public function getFormatter()
    {
        $formatter = parent::getFormatter();

        if ($formatter instanceof ViewResultFormatter) {
            $formatter->layout = $this->layout;
            $formatter->title = $this->title;
            $formatter->view = $this->view;
            $formatter->mode = $this->mode;
            $formatter->viewContext = $this->viewContext;
        }

        return $formatter;
    }
}