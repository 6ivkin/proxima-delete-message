<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME' => 'Модули: Список',
    'DESCRIPTION' => 'Компонент для управления модулями Proxima',
    'PATH' => [
        'ID' => 'Proxima',
        'NAME' => 'Проксима',
        'CHILD' => [
            'ID' => 'service',
            'NAME' => 'Сервис',
        ]
    ],
    'COMPLEX' => 'Y'
];