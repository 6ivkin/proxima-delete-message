<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Проверка модуля',
    'DESCRIPTION' => 'Компонент для проверки файлов модуля',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'Proxima',
        'NAME'  => 'Проксима',
        'CHILD' => [
            'ID'   => 'service',
            'NAME' => 'Сервисы',
        ],
    ],
    'COMPLEX' => 'N',
];