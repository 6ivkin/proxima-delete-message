<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Количество памяти сообщений по пользователям',
    'DESCRIPTION' => 'Отображает количество занимаемой памяти в базе данных сообщениями пользователей.',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'Proxima',
        'NAME'  => 'Проксима',
        'CHILD' => [
            'ID'   => 'messages',
            'NAME' => 'Работа с сообщения',
        ],
    ],
    'COMPLEX' => 'Y',
];