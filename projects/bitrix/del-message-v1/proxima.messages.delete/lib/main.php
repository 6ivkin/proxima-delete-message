<?php

namespace Proxima\Messages\Delete;

use Bitrix\Main\UserTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ORM\Fields;

class Main
{
    public static function delete(): void
    {
        global $DB;

        $month = Option::get('proxima.messages.delete', 'months');
        $user = Option::get('proxima.messages.delete', 'users');
        $switch = Option::get('proxima.messages.delete', 'switch_on');

        $user = explode(',', $user);


        $sqlQuery = 'DELETE
                    FROM b_im_message
                    WHERE date_create < now() - INTERVAL ' . $month . ' MONTH';

        if ($user[0] != '') {
            if ($switch == 'Y') {
                $sqlQuery .= ' AND author_id IN (' . $user[0];
                if (count($user) > 1) {
                    for ($i = 1; $i < count($user); $i++) {
                        $sqlQuery .= ", " . $user[$i];
                    }
                }
                $sqlQuery .= ')';
            } else {
                $sqlQuery .= ' AND author_id NOT IN (' . $user[0];
                if (count($user) > 1) {
                    for ($i = 1; $i < count($user); $i++) {
                        $sqlQuery .= ", " . $user[$i];
                    }
                }
                $sqlQuery .= ')';
            }
        }

        $DB->Query($sqlQuery);
    }

    public static function deleteMessages(int $id): bool
    {
        global $DB;

        $sqlQuery = 'DELETE
                    FROM b_im_message
                    WHERE author_id = ' . $id;

        $DB->Query($sqlQuery);

        return true;
    }

    public static function getMemory(int $user_id): string
    {
        global $DB;

        $sqlResult = 'SELECT message FROM b_im_message WHERE author_id = ' . $user_id;
        $queryResult = $DB->Query($sqlResult);

        $size = 0;
        $result = '';

        while ($res = $queryResult->fetch()) {
            $size += strlen($res['message']);
        }

        if ($size <= 1024) {
            $result = number_format($size / 1024, 4) . ' KB';
        } else if ($size <= 1048576) {
            $result = number_format($size / 1048576, 5) . ' MB';
        } else if ($size <= 1073741824) {
            $result = number_format($size / 1073741824, 4) . ' GB';
        }

        return $result;
    }

    // public static function agent() {
    //     Fedor\Orm\Main::delete();

    //     return "agent();";
    // }

}