<?php


namespace Proxima\Service\Component\Form;


/**
 * Class DateField
 * @package Proxima\Service\Component\Form
 */
class DateField extends Field
{
    const DATE_FORMAT = 'd.m.Y';
    const DATETIME_FORMAT = 'd.m.Y H:i';

    /**
     * @param $value
     * @return false|string
     */
    public function cast($value): string
    {
        if (!empty($value)) {
            if ($this->isUseTime()) {
                return date(self::DATETIME_FORMAT, strtotime($value));
            } else {
                return date(self::DATE_FORMAT, strtotime($value));
            }
        } else {
            return '';
        }
    }

    /**
     * @return bool
     */
    public function isUseTime(): bool
    {
        return boolval($this->options['time']);
    }
}