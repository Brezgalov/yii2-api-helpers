<?php

namespace Brezgalov\ApiHelpers\v2\Validators;

use yii\base\Model;

abstract class RequiredFieldModelValidator
{
    /**
     * @param Model $model
     * @param array $fields
     * @param string $msg
     * @return bool
     */
    public static function validate(Model $model, array $fields, string $msg = ''): bool
    {
        $msg = $msg ?: 'Необходимо указать {attribute}';
        $valid = true;

        foreach ($fields as $alias => $field) {
            if (empty($model->{$field})) {
                $valid = false;

                $model->addError(
                    is_string($alias) ? $alias : $field,
                    str_replace('{attribute}', $model->getAttributeLabel($field), $msg)
                );
            }
        }

        return $valid;
    }
}