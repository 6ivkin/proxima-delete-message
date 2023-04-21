<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Очистка системы',
    'DESCRIPTION' => 'Компонент очистки системы',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'Proxima',
        'NAME'  => 'Проксима',
        'CHILD' => [
            'ID'   => 'service',
            'NAME' => 'Сервис',
        ],
    ],
];