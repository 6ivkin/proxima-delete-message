<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class proxima_messages_delete extends CModule
{
    /**
     * delete_message constructor.
     */
    public function __construct()
    {
        $this->MODULE_ID = 'proxima.messages.delete';
        $this->MODULE_NAME = '[Proxima] Очистка чатов';
        $this->MODULE_DESCRIPTION = 'Модуль для очистки чатов';
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->PARTNER_NAME = 'Proxima';
        $this->PARTNER_URI = 'https://proxima.ooo';
    }


    /**
     *
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            CAdminMessage::ShowNote('Модуль установлен');
        } else {
            CAdminMessage::ShowNote('Ошибка установки модуля');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }


    /**
     *
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            CAdminMessage::ShowNote('Модуль удален');
        } else {
            CAdminMessage::ShowNote('Ошибка удаления модуля');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }


}