<?php

use Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die;

global $APPLICATION, $USER;

$moduleId = $_GET['mid'];
if (!$USER->IsAdmin())
    return;

Loader::includeModule($moduleId);
$tabs = [
    [
        'DIV' => 'general',
        'TAB' => 'Общее',
        'TITLE' => 'Общие настройки'
    ],
];

$options = [
];

if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($moduleId, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();
?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <? foreach ($options as $tab => $option) : ?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($moduleId, $options[$tab]); ?>
    <? endforeach; ?>
    <? $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]); ?>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>