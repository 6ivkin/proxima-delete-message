<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\UI\Extension;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;
use Bitrix\UI\Buttons\JsCode;
use Bitrix\UI\Toolbar\Facade\Toolbar;

Extension::load(['ui.alerts', 'ui.dialogs.messagebox', 'proxima.bootstrap4']); //Подключаем нужные для шаблона js расширения

/**@var CAllMain $APPLICATION */
/**@var CBitrixComponentTemplate $this */
/**@var CITBServiceModuleCheck $component */
$component = $this->getComponent();
?>
<?php  if (true) /* if ($component->getRoute()->getAction() == 'list') */ : ?>
    <?php $APPLICATION->SetTitle('Проверка модуля '.$component->moduleName);  ?>
    <?php foreach($component->getErrorsCompatible() as $error) :  ?>
        <div class="ui-alert ui-alert-danger">
            <span class="ui-alert-message"><?= $error->getMessage() ?></span>
        </div>
    <?php endforeach; ?>

    <?
    if ($component->getGrid())
    $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
        'GRID_ID' => $component->getGrid()->getGridId(),
        'COLUMNS' => $component->getGrid()->getColumns(),
        'ROWS' => $component->getGrid()->getRows(),
        'NAV_OBJECT' => $component->getGrid()->getNavigation(),
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => [
            ['NAME' => '5', 'VALUE' => '5'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'TOTAL_ROWS_COUNT' => $component->getGrid()->getNavigation()->getRecordCount(),
        'AJAX_OPTION_JUMP' => 'N',
        'SHOW_ROW_CHECKBOXES' => false,
        'SHOW_CHECK_ALL_CHECKBOXES' => false,
        'SHOW_ROW_ACTIONS_MENU' => true,
        'SHOW_GRID_SETTINGS_MENU' => true,
        'SHOW_NAVIGATION_PANEL' => false,
        'SHOW_PAGINATION' => false,
        'SHOW_SELECTED_COUNTER' => true,
        'SHOW_TOTAL_COUNTER' => true,
        'SHOW_PAGESIZE' => true,
        'SHOW_ACTION_PANEL' => true,
        'ALLOW_COLUMNS_SORT' => false,
        'ALLOW_COLUMNS_RESIZE' => true,
        'ALLOW_HORIZONTAL_SCROLL' => true,
        'ALLOW_SORT' => false,
        'ALLOW_PIN_HEADER' => true,
        'AJAX_OPTION_HISTORY' => 'N',
    ]);
    ?>

<?php else: ?>
<?php endif; ?>
