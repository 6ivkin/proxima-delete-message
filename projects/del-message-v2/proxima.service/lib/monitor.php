<?php

namespace Proxima\Service;

use Bitrix\Main\Config\Option;

class Monitor
{
    /**
     * @return string
     */
    public static function agent(): string
    {
        try {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $oneMbInByte = 1048576;
            $freeSpace = floatval(disk_free_space($documentRoot) / $oneMbInByte);
            $totalSpace = floatval(disk_total_space($documentRoot) / $oneMbInByte);
            $thresholdMb = Option::get('proxima.service', 'monitor_threshold_hdd');
            $serverName = !empty(SITE_SERVER_NAME) ? SITE_SERVER_NAME : $_SERVER['SERVER_NAME'];
            $alertHdd = ($thresholdMb > $freeSpace);

            if ($alertHdd) {
                $isSend = Option::get('proxima.service', 'monitor_notify_email_active');
                $emails = trim(Option::get('proxima.service', 'monitor_notify_email_list'));
                if ($isSend == 'Y' && strlen($emails)) {
                    $emails = explode(',', $emails);
                    $freeSpaceToSend = number_format($freeSpace, 2);
                    $message = 'На сервере '.$serverName.' заканчивается память на данный момент свободно '.$freeSpaceToSend.' Mb';
                    foreach ($emails as $email) {
                        $sendTo = trim($email);
                        if (!empty($sendTo)) {
                            mail($sendTo, 'Предупреждение !!!!', $message);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::addDef($e->getMessage(), Log::LEVEL_ERROR);
        }
        return '\\' . __METHOD__ . '();';
    }
}