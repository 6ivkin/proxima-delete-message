<?php


namespace Proxima\Service\Component\Form;


/**
 * Class CrmField
 * @package Proxima\Service\Component\Form
 */
class CrmField extends Field
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
    public function isUseLeads(): bool
    {
        return boolval($this->options['use_leads']);
    }

    /**
     * @return bool
     */
    public function isUseDeals(): bool
    {
        return boolval($this->options['use_deals']);
    }

    /**
     * @return bool
     */
    public function isUseContacts(): bool
    {
        return boolval($this->options['use_contacts']);
    }

    /**
     * @return bool
     */
    public function isUseCompanies(): bool
    {
        return boolval($this->options['use_companies']);
    }

    /**
     * @return bool
     */
    public function isUseProducts(): bool
    {
        return boolval($this->options['use_products']);
    }

    /**
     * @return bool
     */
    public function isUseQuotes(): bool
    {
        return boolval($this->options['use_quotes']);
    }

    /**
     * @return bool
     */
    public function isUseOrders(): bool
    {
        return boolval($this->options['use_orders']);
    }

    public function isUseOnlyMyCompanies(): bool
    {
        return boolval($this->options['only_my_companies']);
    }
}