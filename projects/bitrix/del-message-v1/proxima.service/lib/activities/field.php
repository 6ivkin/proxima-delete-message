<?php


namespace Proxima\Service\Activities;


/**
 * Class Field
 * @package Proxima\Service\Activities
 */
class Field
{
    protected string $id;
    protected string $name;
    protected string $type;
    protected bool $required;
    protected $options;
    protected $defaultValue;
    protected bool $multiple;

    /**
     * Field constructor.
     * @param string $id
     * @param string $name
     * @param string $type
     * @param bool $required
     * @param null $options
     * @param null $defaultValue
     * @param bool $multiple
     */
    public function __construct(string $id, string $name, string $type, bool $required, $options = null, $defaultValue = null, bool $multiple = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->options = $options;
        $this->defaultValue = $defaultValue;
        $this->multiple = $multiple;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}