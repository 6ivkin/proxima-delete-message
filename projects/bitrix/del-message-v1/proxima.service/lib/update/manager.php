<?php

namespace Proxima\Service\Update;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\HttpClient;
use Exception;
use Proxima\Service\Log;

class Manager
{
    protected const PREFIX = 'proxima.';
    protected const UPDATE_SERVER_URL = 'https://app3.proxima.com/api/service/v1/';

    /**
     * @param string $moduleId
     * @return bool
     */
    public static function checkModuleId(string $moduleId): bool
    {
        return mb_strpos($moduleId, self::PREFIX) === 0;
    }

    /**
     * @return array
     */
    public static function getModuleList(): array
    {
        $list = array_filter(
            ModuleManager::getInstalledModules(),
            function ($id) {
                return self::checkModuleId($id);
            },
            ARRAY_FILTER_USE_KEY
        );
        return array_keys($list);
    }

    /**
     * @param string $moduleId
     * @return void
     */
    public static function update(string $moduleId): bool
    {
        $log = new Log('updater');
        try {
            $log->add('-----------------------------------------------------------');
            $log->add('Обновление модуля ' . $moduleId);

            //Check
            if (!self::checkModuleId($moduleId)) {
                throw new Exception('Некорректный модуль ' . $moduleId);
            }
            if (!ModuleManager::isModuleInstalled($moduleId)) {
                throw new Exception('Модуль ' . $moduleId . ' не установлен');
            }

            //Get download url
            $client = new HttpClient();
            $client->setHeader('x-portal-id', Application::getInstance()->getContext()->getRequest()->getHttpHost());
            $client->setHeader('x-portal-key', Option::get('proxima.service', 'general_send_statistic_key', ''));
            $client->post(self::UPDATE_SERVER_URL . 'update/' . $moduleId);
            $response = json_decode($client->getResult(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Ошибка разбора ответа от сервера обновлений');
            }
            if ($response['error']) {
                throw new Exception('Ошибка сервера обновлений: ' . $response['description']);
            }
            if (empty($response['url'])) {
                throw new Exception('Некорректный ответ от сервера обновлений');
            }
            $downloadUrl = $response['url'];
            $log->add('Получен url для загрузки ' . $downloadUrl, Log::LEVEL_OK);

            //Create temp directory
            $downloadDirRelPath = '/upload/proxima.service/update/' . $moduleId . '/' . time() . '-' . uniqid() . '/';
            $downloadDirPath = $_SERVER['DOCUMENT_ROOT'] . $downloadDirRelPath;
            $downloadFileName = 'update.zip';
            $archivePath = $downloadDirPath . $downloadFileName;
            if (!mkdir($downloadDirPath, 0755, true)) {
                throw new Exception('Ошибка создания папки для загрузки');
            }
            $log->add('Создана временная директория ' . $downloadDirPath, Log::LEVEL_OK);

            //Download archive
            $client = new HttpClient();
            $client->setHeader('x-portal-id', Application::getInstance()->getContext()->getRequest()->getHttpHost());
            $client->setHeader('x-portal-key', Option::get('proxima.service', 'general_send_statistic_key', ''));
            $client->post($downloadUrl);
            if ($client->getStatus() !== 200) {
                $response = json_decode($client->getResult(), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Ошибка разбора ответа от сервера обновлений');
                }
                if ($response['error']) {
                    throw new Exception('Ошибка сервера обновлений: ' . $response['description']);
                } else {
                    throw new Exception('Неизвестная ошибка');
                }
            }

            $testHash = $client->getHeaders()->get('x-archive-cs');
            $log->add('Получена КС архива ' . $testHash, Log::LEVEL_OK);

            file_put_contents($archivePath, $client->getResult());
            if (!file_exists($archivePath)) {
                throw new Exception('Ошибка загрузки файла');
            }
            $hash = hash_file('md5', $archivePath);
            $log->add('Обновления загружены ' . $archivePath . ' размер ' . filesize($archivePath) . ' байт КС ' . $hash, Log::LEVEL_OK);

            //Check hash
            if ($hash !== $testHash) {
                throw new Exception('Контрольные суммы не сходятся');
            }

            //Create temp dir for unpack
            $modulePath = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $moduleId . '/';
            $tempPath = $downloadDirPath . 'module/';
            if (!mkdir($tempPath, 0755, true)) {
                throw new Exception('Ошибка создания папки для распаковки');
            }
            $log->add('Создана директория для распаковки ' . $tempPath, Log::LEVEL_OK);

            //Unpack archive
            $zip = new \ZipArchive();
            if (!$zip->open($archivePath)) {
                throw new Exception('Ошибка открытия архива');
            }
            if (!$zip->extractTo($tempPath)) {
                $zip->close();
                throw new Exception('Ошибка распаковки архива');
            } else {
                $zip->close();
            }
            $log->add('Архив распакован', Log::LEVEL_OK);

            //Backup module
            $backupPath = $downloadDirPath . 'backup/';
            if(!CopyDirFiles($modulePath, $backupPath, true, true)) {
                throw new Exception('Ошибка создания резервной копии модуля');
            }
            $log->add('Резервная копия модуля сохранена в ' . $backupPath, Log::LEVEL_OK);

            //Copy files
            if(!CopyDirFiles($tempPath, $modulePath, true, true)) {
                throw new Exception('Ошибка копирования файлов модуля');
            }
            $log->add('Файлы скопированы в ' . $modulePath, Log::LEVEL_OK);

            //Remove temp files
            if (!empty($downloadDirRelPath)) {
                if (DeleteDirFilesEx($downloadDirRelPath)) {
                    $log->add('Временные файлы удалены', Log::LEVEL_OK);
                } else {
                    $log->add('Ошибка удаления временной директории ' . $downloadDirPath, Log::LEVEL_WARN);
                }
            }

            //Install procedure
            $module = \CModule::CreateModuleObject($moduleId);
            if ($module) {
                $module->InstallFiles();
                $module->InstallDB();
                $module->UnInstallEvents();
                $module->InstallEvents();
                if (method_exists($module, 'InstallAgents')) {
                    if (method_exists($module, 'UnInstallAgents')) {
                        $module->UnInstallAgents();
                    }
                    $module->InstallAgents();
                }
            } else {
                $log->add('Ошибка получения объекта модуля ' . $downloadDirPath, Log::LEVEL_WARN);
            }

            $log->add('Обновления успешно установлены, версия модуля ' . $module->MODULE_VERSION, Log::LEVEL_OK);
            return true;
        } catch (Exception $e) {
            $log->add($e->getMessage(), Log::LEVEL_ERROR);
            return false;
        }
    }
}