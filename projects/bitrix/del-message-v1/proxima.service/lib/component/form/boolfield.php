<?php


namespace Proxima\Service\Component\Form;


/**
 * Class SelectField
 * @package Proxima\Service\Component\Form
 */
class BoolField extends Field
{
    /**
     * @param $value
     * @return bool
     */
    public function cast($value)
    {
        return boolval($value);
    }
}