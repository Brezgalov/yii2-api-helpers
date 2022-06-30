<?php

namespace Brezgalov\ApiHelpers\v2\Formatters;

class SendCsvFormatter extends SendFileFormatter
{
    /**
     * @var array|callable
     */
    public $headers;

    /**
     * @var string
     */
    public $tmpFilePath = '@app/runtime/';

    /**
     * @param object $service
     * @param mixed $result
     * @return \yii\base\Model|\yii\web\Response
     */
    public function format($service, $result)
    {
        if (!is_array($result)) {
            return parent::format($service, $result);
        }

        if (is_array($this->headers)) {
            array_unshift($result, $this->headers);
        }

        $filePath = \Yii::getAlias($this->tmpFilePath . uniqid('file_') . '.csv');
        $fp = fopen($filePath, 'w');

        foreach ($result as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        return parent::format($service, $filePath);
    }
}