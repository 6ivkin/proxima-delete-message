<?php


namespace Proxima\Service\Component\Form;


/**
 * Class StringField
 * @package Proxima\Service\Component\Form
 */
class UserField extends Field
{
    /**
     * @param $value
     * @return array
     */
    public function cast($value)
    {
        return is_array($value) ? $value : [$value];
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return boolval($this->options['multiple']);
    }

    /**
     * @return bool
     */
    public function isUseSymbolic(): bool
    {
        return boolval($this->options['use_symbolic_id']);
    }

    /**
     * @return bool
     */
    public function isEnableDepartments(): bool
    {
        return boolval($this->options['enable_departments']);
    }

    /**
     * @return bool
     */
    public function isDisableUsers(): bool
    {
        return boolval($this->options['disable_users']);
    }


}