<?php


namespace Proxima\Service\Component\Form;


/**
 * Class StringField
 * @package Proxima\Service\Component\Form
 */
class StringField extends Field
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
    public function getPattern()
    {
        return strval($this->options['pattern']);
    }

    /**
     * @return string
     */
    public function getMaxLength()
    {
        return strval($this->options['maxlength']);
    }

}