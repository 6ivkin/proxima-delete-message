<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Аннотация модулей',
    'DESCRIPTION' => 'Компонент для генерации аннотаций ORM к модулям',
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