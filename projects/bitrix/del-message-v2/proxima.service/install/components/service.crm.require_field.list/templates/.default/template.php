<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Extension::load(['ui.alerts', 'ui.forms', 'proxima.bootstrap4']);

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceCrmRequireFieldList $component */
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
<?php $APPLICATION->SetTitle("Обязательные поля"); ?>
<?php foreach($component->getErrorsCompatible() as $error) :?>
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><?= $error->getMessage() ?></span>
    </div>
<?php endforeach; ?>
<?php if($component->getGrid()!=null):?>
    <?php
    Toolbar::addFilter([
        "GRID_ID" => $component->getGrid()->getGridId(),
        "FILTER_ID" => $component->getGrid()->getFilterId(),
        "FILTER" => $component->getGrid()->getFilter(),
        "ENABLE_LIVE_SEARCH" => true,
        "ENABLE_LABEL" => true,
        "RESET_TO_DEFAULT_MODE" => true,
    ]);
    ?>
    <?php $APPLICATION->IncludeComponent("bitrix:main.ui.grid", "", [
        "GRID_ID" => $component->getGrid()->getGridId(),
        "COLUMNS" => $component->getGrid()->getColumns(),
        "ROWS" => $component->getGrid()->getRows(),
        "NAV_OBJECT" => $component->getGrid()->getNavigation(),
        "SHOW_ROW_CHECKBOXES" => false,
        "AJAX_MODE" => "Y",
        "AJAX_ID" => CAjax::getComponentID("bitrix:main.ui.grid", ".default", ""),
        "PAGE_SIZES" => [
            ["NAME" => "5", "VALUE" => "5"],
            ["NAME" => "20", "VALUE" => "20"],
            ["NAME" => "50", "VALUE" => "50"],
            ["NAME" => "100", "VALUE" => "100"]
        ],
        "TOTAL_ROWS_COUNT" => $component->getGrid()->getNavigation()->getRecordCount(),
        "AJAX_OPTION_JUMP" => "N",
        "SHOW_CHECK_ALL_CHECKBOXES" => false,
        "SHOW_ROW_ACTIONS_MENU" => true,
        "SHOW_GRID_SETTINGS_MENU" => true,
        "SHOW_NAVIGATION_PANEL" => true,
        "SHOW_PAGINATION" => false,
        "SHOW_SELECTED_COUNTER" => false,
        "SHOW_TOTAL_COUNTER" => true,
        "SHOW_PAGESIZE" => false,
        "SHOW_ACTION_PANEL" => false,
        "ALLOW_COLUMNS_SORT" => true,
        "ALLOW_COLUMNS_RESIZE" => true,
        "ALLOW_HORIZONTAL_SCROLL" => true,
        "ALLOW_SORT" => true,
        "ALLOW_PIN_HEADER" => true,
        "AJAX_OPTION_HISTORY" => "N",
    ]); ?>
<?php endif;?>

