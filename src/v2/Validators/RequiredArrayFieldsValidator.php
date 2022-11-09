<?php

namespace Brezgalov\ApiHelpers\v2\Validators;

use yii\base\Model;

class RequiredArrayFieldsValidator
{
    /**
     * @var string
     */
    public $errorMsgPattern;

    /**
     * @var Model
     */
    private $errorModel;

    /**
     * RequiredArrayFieldsValidator constructor.
     * @param Model|null $errorModel
     * @param string $errorMsgPattern
     */
    public function __construct(Model $errorModel = null, string $errorMsgPattern = 'Необходимо указать {attribute}')
    {
        $this->errorModel = $errorModel ?: new Model();
        $this->errorMsgPattern = $errorMsgPattern;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function prepareErrorMsg(string $fieldName): string
    {
        $attributeLabel = $this->errorModel->getAttributeLabel($fieldName);

        return str_replace('{attribute}', $attributeLabel, $this->errorMsgPattern);
    }

    /**
     * @param array $input
     * @param array $requiredFields
     * @return bool
     */
    public function validateInput(array $input, array $requiredFields): bool
    {
        $valid = true;

        foreach ($requiredFields as $field) {
            if (array_key_exists($field, $input) && !empty($input[$field])) {
                continue;
            }

            $valid = false;
            $this->errorModel->addError(
                $field,
                $this->prepareErrorMsg($field)
            );
        }

        return $valid;
    }

    /**
     * @return Model
     */
    public function getErrorModel(): Model
    {
        return $this->errorModel;
    }
}
