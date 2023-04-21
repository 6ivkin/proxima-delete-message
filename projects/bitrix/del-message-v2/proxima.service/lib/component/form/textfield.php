<?php


namespace Proxima\Service\Component\Form;


/**
 * Class TextField
 * @package Proxima\Service\Component\Form
 */
class TextField extends Field
{
    /**
     * @param $value
     * @return string
     */
    public function cast($value)
    {
        return strval($value);
    }

    /**
     * @return string
     */
    public function getMaxLength()
    {
        return strval($this->options['maxlength']);
    }

}