<?php

namespace Brezgalov\ApiHelpers;

interface IApiInputForm
{
    /**
     * @param array $args
     * @return mixed
     */
    public function getResult(array $args = []);

    /**
     * @param array $params
     * @param string $formName
     * @return mixed
     */
    public function load($params, $formName = '');

    /**
     * @return bool
     */
    public function validate();

    /**
     * @param $attribute
     * @param $error
     * @return mixed
     */
    public function addError($attribute, $error);

    /**
     * @return bool
     */
    public function hasErrors();
}