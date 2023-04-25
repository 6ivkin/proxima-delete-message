<?php
use Bitrix\Main\UI\Extension;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Extension::load(['ui.alerts', 'ui.dialogs.messagebox', 'proxima.bootstrap4', "ui.buttons"]);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var ServiceModuleList $component */
$component = $this->getComponent();
?>
<div class="p-3">
    <?php
    $APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        "",
        Array(
            "PATH" => "",
            "SITE_ID" => "s1",
            "START_FROM" => "2"
        )
    );
    ?>
</div>
<?php if ($component->getRoute()->getAction() === $component->getRoute()->getDefaultAction()) : ?>
    <?php $APPLICATION->SetTitle('Модули'); ?>
    <?php foreach($component->getErrorsCompatible() as $error) :  ?>
        <div class="ui-alert ui-alert-danger">
            <span class="ui-alert-message"><?= $error->getMessage() ?></span>
        </div>
    <?php endforeach; ?>
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => $component->getGrid()->getGridId(),
            'COLUMNS' => $component->getGrid()->getColumns(),
            'ROWS' => $component -> getGrid() -> getRows(),
            'SHOW_ROW_CHECKBOXES' => false,
            'NAV_OBJECT' => $component->getGrid()->getNavigation(),
            'AJAX_MODE' => 'Y',
            'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', '')   ,
            'PAGE_SIZES' => [
                ['NAME' => "5", 'VALUE' => '5'],
                ['NAME' => '10', 'VALUE' => '10'],
                ['NAME' => '20', 'VALUE' => '20'],
                ['NAME' => '50', 'VALUE' => '50'],
                ['NAME' => '100', 'VALUE' => '100']
            ],
            'TOTAL_ROWS_COUNT' => 0,
            'AJAX_OPTION_JUMP'          => 'N',
            'SHOW_CHECK_ALL_CHECKBOXES' => false,
            'SHOW_ROW_ACTIONS_MENU'     => true,
            'SHOW_GRID_SETTINGS_MENU'   => false,
            'SHOW_NAVIGATION_PANEL'     => false,
            'SHOW_PAGINATION'           => false,
            'SHOW_SELECTED_COUNTER'     => false,
            'SHOW_TOTAL_COUNTER'        => false,
            'SHOW_PAGESIZE'             => false,
            'SHOW_ACTION_PANEL'         => false,
            'ALLOW_COLUMNS_SORT'        => false,
            'ALLOW_COLUMNS_RESIZE'      => true,
            'ALLOW_HORIZONTAL_SCROLL'   => true,
            'ALLOW_SORT'                => false,
            'ALLOW_PIN_HEADER'          => true,
            'AJAX_OPTION_HISTORY'       => 'N'
        ]
    );
    ?>
<?php else: ?>
    <?php
    $APPLICATION->IncludeComponent(
        'bitrix:ui.sidepanel.wrapper',
        '',
        [
            'POPUP_COMPONENT_NAME' => 'proxima:service.module.' . $component->getRoute()->getAction(),
            'POPUP_COMPONENT_TEMPLATE_NAME' => '',
            'POPUP_COMPONENT_PARAMS' => ['HELPER' => $component->getRoute()],
            'CLOSE_AFTER_SAVE' => true,
            'RELOAD_GRID_AFTER_SAVE' => true,
            'RELOAD_PAGE_AFTER_SAVE' => false,
            'PAGE_MODE' => false,
            'PAGE_MODE_OFF_BACK_URL' => $component->getRoute()->getUrl($component->getRoute()->getDefaultAction()),
            'USE_PADDING' => true,
            'PLAIN_VIEW' => false,
            'USE_UI_TOOLBAR' => 'Y',
        ]
    );
    ?>
<?php endif; ?>