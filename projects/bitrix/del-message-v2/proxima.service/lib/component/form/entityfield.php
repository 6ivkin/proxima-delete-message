<?php


namespace Proxima\Service\Component\Form;


use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

class EntityField extends Field
{
    protected array $modules;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->modules = [];
    }

    /**
     * @param $value
     * @return Field
     */
    public function setValue($value): Field
    {
        parent::setValue($value);
        if (is_array($this->value)) {
            $this->value = array_filter(
                $this->value,
                function ($item) {
                    return is_array($item) && !empty($item['id']) && !empty($item['entityId']);
                }
            );
        } else {
            $this->value = [];
        }
        return $this;
    }

    /**
     * @param $value
     * @return Field
     */
    public function setOption($value): Field
    {
        parent::setOption($value);
        if (isset($this->options['entities']) && is_array($this->options['entities'])) {
            $this->options['entities'] = array_filter(
                $this->options['entities'],
                function ($item) {
                    $valid = is_array($item) && !empty($item['id']) && !empty($item['moduleId']);
                    if ($valid && !isset($this->modules[$item['moduleId']])) {
                        $this->modules[$item['moduleId']] = strval($item['moduleId']);
                    }
                    return $valid;
                }
            );
        } else {
            $this->options['entities'] = [];
        }


        return $this;
    }

    /**
     * @return void
     * @throws LoaderException
     */
    public function includeModules()
    {
        foreach ($this->modules as $moduleId) {
            Loader::includeModule($moduleId);
        }
    }

    /**
     * @param $value
     * @return array
     */
    public function cast($value)
    {
        return is_array($value) ? $value : [];
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
    public function isDeselectable(): bool
    {
        return !isset($this->options['deselectable']) || boolval($this->options['deselectable']);
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        return (is_array($this->options['entities'])) ? $this->options['entities'] : [];
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return isset($this->options['context']) ? strval($this->options['context']) : 'Proxima_SERVICE_ENTITY_' . mb_strtoupper($this->getName());
    }

    /**
     * @param string $json
     * @return array
     */
    public static function parseValue(string $json): array
    {
        $data = json_decode($json, true);
        if (is_array($data)) {
            $data = array_filter(
                $data,
                function ($item) {
                    return is_array($item) && !empty($item['id']) && !empty($item['entityId']);
                }
            );
            return $data;
        } else {
            return [];
        }
    }
}