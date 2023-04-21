<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

/**
 * Class proxima_service
 */
class proxima_service extends CModule
{
    /**
     * proxima_service constructor.
     */
    public function __construct()
    {
        $this->MODULE_ID = 'proxima.service';
        $this->MODULE_NAME = '[Proxima] Сервис';
        $this->MODULE_DESCRIPTION = 'Сервисный модуль';
        include(__DIR__.'/version.php');
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->PARTNER_NAME = 'Proxima';
        $this->PARTNER_URI = 'https://proxima.com';
    }

    /**
     * Install
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallEvents();
            if(!$this->InstallFiles())
                return false;
            $this->InstallAgents();
            CAdminMessage::ShowNote('Модуль установлен');
        } else {
            CAdminMessage::ShowNote('Ошибка установки модуля');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Uninstall
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallAgents();
            CAdminMessage::ShowNote('Модуль удален');
        } else {
            CAdminMessage::ShowNote('Ошибка удаления модуля');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool|void
     */
    public function InstallFiles()
    {
        if(!CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/proxima', true, true)) {
            $GLOBALS["APPLICATION"]->ThrowException( 'Ошибка при копировании файлов модуля');
            return false;
        }

        if(!CopyDirFiles(__DIR__ . '/public', $_SERVER['DOCUMENT_ROOT'] . '/proxima/service', true, true)) {
            $GLOBALS["APPLICATION"]->ThrowException( 'Ошибка при копировании файлов модуля');
            return false;
        }

        \Bitrix\Main\UrlRewriter::reindexFile(SITE_ID, CSite::GetSiteDocRoot(SITE_ID), '/proxima/service/modules/index.php');
        return true;
    }

    /**
     * @return bool|void
     */
    public function UnInstallFiles()
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function InstallAgents(): void
    {
        CAgent::AddAgent("\\Proxima\\Service\\Monitor::agent();",
            $this->MODULE_ID,
            "Y",
            3600,
            "",
            "Y",
            \Bitrix\Main\Type\DateTime::createFromPhp((new DateTime())->setTime(date('H') + 1, 0, 0)),
            20);
    }

    /**
     *
     */
    public function UnInstallAgents(): void
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}