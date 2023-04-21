<?php


namespace Proxima\Service;

use Bitrix\Main\ModuleManager;

/**
 * Class Utils
 * @package Proxima\Service
 */
class Utils
{
    protected static ?array $extensions = null;

    /**
     * Return required PHP extension list
     * @return string[]
     */
    public static function getRequiredExtensionList(): array
    {
        return ['curl', 'json', 'zip', 'Phar'];
    }

    /**
     * Get loaded and active PHP extensions list
     * @return array
     */
    public static function getExtensionList(): array
    {
        if(!self::$extensions) {
            self::$extensions = get_loaded_extensions();
        }
        return self::$extensions;
    }

    /**
     * Check extension activity by name
     * @param string $extension
     * @return bool
     */
    public static function isExtensionActive(string $extension): bool
    {
        return in_array($extension, self::getExtensionList());
    }

    /**
     * Return required Bitrix modules list
     * @return string[]
     */
    public static function getRequiredModuleList(): array
    {
        return ['main', 'mail', 'crm', 'tasks'];
    }

    /**
     * @param string $module
     * @return bool
     */
    public static function isModuleInstalled(string $module): bool
    {
        return ModuleManager::isModuleInstalled($module);
    }

    /**
     * Check system for requirements
     * @param array $messages - array of output messages
     * @return bool
     */
    public static function checkEngine(array &$messages): bool
    {
        $result = true;

        //Check PHP extensions
        foreach(self::getRequiredExtensionList() as $extension) {
            if(!self::isExtensionActive($extension)) {
                $messages[] = 'Раширение PHP '.$extension.' не найдено';
                $result &= false;
            }
        }

        //Check bitrix modules
        foreach(self::getRequiredModuleList() as $module) {
            if(!self::isModuleInstalled($module)) {
                $messages[] = 'Модуль Битрикс '.$module.' не установлен';
                $result &= false;
            }
        }

        if($result) {
            $messages[] = 'Все требования выполнены';
        }
        return $result;
    }

    /**
     * @param string $tableClass
     * @param string $fieldId
     * @param string $fieldName
     * @param array $filter
     * @param array $order
     * @return array
     */
    public static function getItemsList(string $tableClass, string $fieldId, string $fieldName, array $filter = [], array $order = []): array
    {
        $result = [];
        $resultDb = $tableClass::getList([
            'select' => [$fieldId, $fieldName],
            'filter' => $filter,
            'order' => $order,
        ]);
        while ($item = $resultDb->fetchObject()) {
            $result[$item->get($fieldId)] = $item->get($fieldName);
        }
        return $result;
    }

}