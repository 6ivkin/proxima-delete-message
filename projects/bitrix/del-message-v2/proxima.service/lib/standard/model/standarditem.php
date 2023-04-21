<?php


namespace Proxima\Service\Standard\Model;


use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\SystemException;
use Proxima\Service\Standard\StandardItem;

/**
 * Class StandardItemTable
 * @package Proxima\Service\Standard\Model
 */
class StandardItemTable extends DataManager
{
    /**
     * @return string|null
     */
    public static function getTitle()
    {
        return 'Всякие штуки';
    }

    /**
     * @return string|null
     */
    public static function getTableName()
    {
        return 'itb_service_standard_item';
    }

    /**
     * @return string
     */
    public static function getObjectClass()
    {
        return StandardItem::class;
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID',
                [
                    'title' => 'ID',
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new Fields\StringField('NAME',
                [
                    'title' => 'Название',
                    'required' => true,
                ]
            ),
        ];
    }
}